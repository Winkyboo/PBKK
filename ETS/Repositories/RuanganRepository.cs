using BookingKampus.Helpers;
using BookingKampus.Models;
using Microsoft.Data.SqlClient;

namespace BookingKampus.Repositories;

public static class RuanganRepository
{
    static Ruangan Map(SqlDataReader r) => new()
    {
        RuanganId = r.GetInt32(0),
        Kode = r.GetString(1),
        Nama = r.GetString(2),
        Gedung = r.GetString(3),
        Lantai = r.GetInt32(4),
        Kapasitas = r.GetInt32(5),
        Deskripsi = r.GetString(6),
        IsActive = r.GetBoolean(7),
        FasilitasText = r.GetString(8)
    };

    public static Task<List<Ruangan>> ListAsync(string search, bool onlyActive = false) =>
        Db.QueryAsync<Ruangan>(
            @"SELECT r.RuanganId, r.Kode, r.Nama, r.Gedung, r.Lantai, r.Kapasitas, ISNULL(r.Deskripsi, ''), r.IsActive,
                     ISNULL((SELECT STRING_AGG(f.Nama, ', ') WITHIN GROUP (ORDER BY f.Nama)
                             FROM dbo.RuanganFasilitas rf
                             JOIN dbo.Fasilitas f ON f.FasilitasId = rf.FasilitasId
                             WHERE rf.RuanganId = r.RuanganId), '')
              FROM dbo.Ruangan r
              WHERE (@q = '' OR r.Kode LIKE '%' + @q + '%' OR r.Nama LIKE '%' + @q + '%' OR r.Gedung LIKE '%' + @q + '%')
                AND (@a = 0 OR r.IsActive = 1)
              ORDER BY r.Kode", Map, Db.P("@q", search ?? ""), Db.P("@a", onlyActive));

    public static Task<List<int>> FasilitasIdsAsync(int ruanganId) =>
        Db.QueryAsync<int>("SELECT FasilitasId FROM dbo.RuanganFasilitas WHERE RuanganId = @r",
            r => r.GetInt32(0), Db.P("@r", ruanganId));

    public static Task<List<Fasilitas>> FasilitasOfRuanganAsync(int ruanganId) =>
        Db.QueryAsync<Fasilitas>(
            @"SELECT f.FasilitasId, f.Nama, ISNULL(f.Deskripsi, ''), f.IsActive
              FROM dbo.RuanganFasilitas rf
              JOIN dbo.Fasilitas f ON f.FasilitasId = rf.FasilitasId
              WHERE rf.RuanganId = @r AND f.IsActive = 1
              ORDER BY f.Nama",
            r => new Fasilitas { FasilitasId = r.GetInt32(0), Nama = r.GetString(1), Deskripsi = r.GetString(2), IsActive = r.GetBoolean(3) },
            Db.P("@r", ruanganId));

    public static async Task<int> SaveAsync(Ruangan room, IList<int> fasilitasIds)
    {
        Session.Require(Roles.Admin);
        await using var c = Db.Create();
        await c.OpenAsync();
        await using var tx = (SqlTransaction)await c.BeginTransactionAsync();
        int id = room.RuanganId;
        var desc = string.IsNullOrWhiteSpace(room.Deskripsi) ? null : room.Deskripsi;

        if (id == 0)
        {
            await using var ins = new SqlCommand(
                @"INSERT INTO dbo.Ruangan (Kode, Nama, Gedung, Lantai, Kapasitas, Deskripsi, IsActive)
                  OUTPUT INSERTED.RuanganId
                  VALUES (@k, @n, @g, @l, @c, @d, @a)", c, tx);
            Fill(ins, room, desc);
            id = (int)await ins.ExecuteScalarAsync();
        }
        else
        {
            await using var upd = new SqlCommand(
                @"UPDATE dbo.Ruangan SET Kode = @k, Nama = @n, Gedung = @g, Lantai = @l, Kapasitas = @c,
                         Deskripsi = @d, IsActive = @a WHERE RuanganId = @id", c, tx);
            Fill(upd, room, desc);
            upd.Parameters.AddWithValue("@id", id);
            await upd.ExecuteNonQueryAsync();
        }

        await using (var del = new SqlCommand("DELETE FROM dbo.RuanganFasilitas WHERE RuanganId = @id", c, tx))
        {
            del.Parameters.AddWithValue("@id", id);
            await del.ExecuteNonQueryAsync();
        }
        foreach (var fid in fasilitasIds.Distinct())
        {
            await using var link = new SqlCommand(
                "INSERT INTO dbo.RuanganFasilitas (RuanganId, FasilitasId) VALUES (@r, @f)", c, tx);
            link.Parameters.AddWithValue("@r", id);
            link.Parameters.AddWithValue("@f", fid);
            await link.ExecuteNonQueryAsync();
        }
        await tx.CommitAsync();
        return id;
    }

    static void Fill(SqlCommand cmd, Ruangan r, string desc)
    {
        cmd.Parameters.AddWithValue("@k", r.Kode);
        cmd.Parameters.AddWithValue("@n", r.Nama);
        cmd.Parameters.AddWithValue("@g", r.Gedung);
        cmd.Parameters.AddWithValue("@l", r.Lantai);
        cmd.Parameters.AddWithValue("@c", r.Kapasitas);
        cmd.Parameters.AddWithValue("@d", (object)desc ?? DBNull.Value);
        cmd.Parameters.AddWithValue("@a", r.IsActive);
    }

    public static async Task DeleteAsync(int id)
    {
        Session.Require(Roles.Admin);
        await Db.ExecAsync("DELETE FROM dbo.Ruangan WHERE RuanganId = @id", Db.P("@id", id));
    }
}
