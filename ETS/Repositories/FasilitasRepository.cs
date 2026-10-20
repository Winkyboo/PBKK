using BookingKampus.Helpers;
using BookingKampus.Models;
using Microsoft.Data.SqlClient;

namespace BookingKampus.Repositories;

public static class FasilitasRepository
{
    static Fasilitas Map(SqlDataReader r) => new()
    {
        FasilitasId = r.GetInt32(0),
        Nama = r.GetString(1),
        Deskripsi = r.IsDBNull(2) ? "" : r.GetString(2),
        IsActive = r.GetBoolean(3)
    };

    public static Task<List<Fasilitas>> ListAsync(string search, bool onlyActive = false) =>
        Db.QueryAsync<Fasilitas>(
            @"SELECT FasilitasId, Nama, Deskripsi, IsActive FROM dbo.Fasilitas
              WHERE (@q = '' OR Nama LIKE '%' + @q + '%') AND (@a = 0 OR IsActive = 1)
              ORDER BY Nama", Map, Db.P("@q", search ?? ""), Db.P("@a", onlyActive));

    public static async Task SaveAsync(Fasilitas f)
    {
        Session.Require(Roles.Admin);
        var desc = string.IsNullOrWhiteSpace(f.Deskripsi) ? null : f.Deskripsi;
        if (f.FasilitasId == 0)
            await Db.ExecAsync("INSERT INTO dbo.Fasilitas (Nama, Deskripsi, IsActive) VALUES (@n, @d, @a)",
                Db.P("@n", f.Nama), Db.P("@d", desc), Db.P("@a", f.IsActive));
        else
            await Db.ExecAsync("UPDATE dbo.Fasilitas SET Nama = @n, Deskripsi = @d, IsActive = @a WHERE FasilitasId = @id",
                Db.P("@n", f.Nama), Db.P("@d", desc), Db.P("@a", f.IsActive), Db.P("@id", f.FasilitasId));
    }

    public static async Task DeleteAsync(int id)
    {
        Session.Require(Roles.Admin);
        await Db.ExecAsync("DELETE FROM dbo.Fasilitas WHERE FasilitasId = @id", Db.P("@id", id));
    }
}
