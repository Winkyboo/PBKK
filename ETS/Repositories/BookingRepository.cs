using BookingKampus.Helpers;
using BookingKampus.Models;
using Microsoft.Data.SqlClient;

namespace BookingKampus.Repositories;

public static class BookingRepository
{
    const string Select = @"
        SELECT b.BookingId, b.UserId, u.Nama, b.RuanganId, r.Nama, b.Tanggal, b.JamMulai, b.JamSelesai,
               b.Keperluan, b.[Status], ISNULL(b.CatatanApprover, ''), b.CreatedAt
        FROM dbo.Booking b
        JOIN dbo.Users u ON u.UserId = b.UserId
        JOIN dbo.Ruangan r ON r.RuanganId = b.RuanganId ";

    static Booking Map(SqlDataReader r) => new()
    {
        BookingId = r.GetInt32(0),
        UserId = r.GetInt32(1),
        PemohonNama = r.GetString(2),
        RuanganId = r.GetInt32(3),
        RuanganNama = r.GetString(4),
        Tanggal = r.GetDateTime(5),
        JamMulai = r.GetTimeSpan(6),
        JamSelesai = r.GetTimeSpan(7),
        Keperluan = r.GetString(8),
        Status = r.GetString(9),
        CatatanApprover = r.GetString(10),
        CreatedAt = r.GetDateTime(11)
    };

    public static async Task<Booking> GetAsync(int id)
    {
        var l = await Db.QueryAsync<Booking>(Select + "WHERE b.BookingId = @id", Map, Db.P("@id", id));
        return l.FirstOrDefault();
    }

    public static Task<List<Booking>> ListByUserAsync(int userId) =>
        Db.QueryAsync<Booking>(Select + "WHERE b.UserId = @u ORDER BY b.Tanggal DESC, b.JamMulai DESC", Map, Db.P("@u", userId));

    public static Task<List<Booking>> ListPendingAsync() =>
        Db.QueryAsync<Booking>(Select + "WHERE b.[Status] = 'Menunggu' ORDER BY b.Tanggal, b.JamMulai", Map);

    /// <summary>Jadwal aktif satu ruangan pada satu tanggal (disetujui dan masih menunggu).</summary>
    public static Task<List<Booking>> ListScheduleAsync(int ruanganId, DateTime tanggal) =>
        Db.QueryAsync<Booking>(
            Select + @"WHERE b.RuanganId = @r AND b.Tanggal = @t AND b.[Status] IN ('Disetujui', 'Menunggu')
                       ORDER BY b.JamMulai", Map, Db.P("@r", ruanganId), Db.P("@t", tanggal.Date));

    public static Task<List<Booking>> ListTodayApprovedAsync() =>
        Db.QueryAsync<Booking>(
            Select + @"WHERE b.Tanggal = CAST(GETDATE() AS date) AND b.[Status] = 'Disetujui'
                       ORDER BY b.JamMulai, r.Nama", Map);

    /// <summary>Memeriksa bentrok dengan booking yang sudah disetujui, dan opsional yang masih menunggu.</summary>
    public static async Task<bool> HasConflictAsync(int ruanganId, DateTime tanggal, TimeSpan mulai, TimeSpan selesai,
                                                    int excludeId, bool includePending = false)
    {
        int n = await Db.ScalarAsync<int>(
            @"SELECT COUNT(*) FROM dbo.Booking
              WHERE RuanganId = @r AND Tanggal = @t
                AND ([Status] = 'Disetujui' OR (@includePending = 1 AND [Status] = 'Menunggu'))
                AND BookingId <> @id
                AND JamMulai < @s AND JamSelesai > @m",
            Db.P("@r", ruanganId), Db.P("@t", tanggal.Date), Db.P("@id", excludeId),
            Db.P("@s", selesai), Db.P("@m", mulai), Db.P("@includePending", includePending));
        return n > 0;
    }

    public static async Task<int> InsertAsync(int userId, int ruanganId, DateTime tanggal, TimeSpan mulai, TimeSpan selesai,
                                              string keperluan, IList<int> fasilitasIds)
    {
        await using var c = Db.Create();
        await c.OpenAsync();
        await using var tx = (SqlTransaction)await c.BeginTransactionAsync();
        int id;
        await using (var cmd = new SqlCommand(
            @"INSERT INTO dbo.Booking (UserId, RuanganId, Tanggal, JamMulai, JamSelesai, Keperluan, [Status])
              OUTPUT INSERTED.BookingId
              VALUES (@u, @r, @t, @m, @s, @k, 'Menunggu')", c, tx))
        {
            cmd.Parameters.AddWithValue("@u", userId);
            cmd.Parameters.AddWithValue("@r", ruanganId);
            cmd.Parameters.AddWithValue("@t", tanggal.Date);
            cmd.Parameters.AddWithValue("@m", mulai);
            cmd.Parameters.AddWithValue("@s", selesai);
            cmd.Parameters.AddWithValue("@k", keperluan);
            id = (int)await cmd.ExecuteScalarAsync();
        }
        foreach (var fid in fasilitasIds.Distinct())
        {
            await using var link = new SqlCommand(
                "INSERT INTO dbo.BookingFasilitas (BookingId, FasilitasId) VALUES (@b, @f)", c, tx);
            link.Parameters.AddWithValue("@b", id);
            link.Parameters.AddWithValue("@f", fid);
            await link.ExecuteNonQueryAsync();
        }
        await tx.CommitAsync();
        return id;
    }

    public static Task<int> SetStatusAsync(int id, string status, string catatan) =>
        Db.ExecAsync("UPDATE dbo.Booking SET [Status] = @s, CatatanApprover = @c WHERE BookingId = @id",
            Db.P("@s", status), Db.P("@c", string.IsNullOrWhiteSpace(catatan) ? null : catatan), Db.P("@id", id));

    public static Task<List<string>> FasilitasNamesAsync(int bookingId) =>
        Db.QueryAsync<string>(
            @"SELECT f.Nama FROM dbo.BookingFasilitas bf JOIN dbo.Fasilitas f ON f.FasilitasId = bf.FasilitasId
              WHERE bf.BookingId = @b ORDER BY f.Nama", r => r.GetString(0), Db.P("@b", bookingId));
}
