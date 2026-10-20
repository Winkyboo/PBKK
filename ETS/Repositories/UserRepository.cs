using BookingKampus.Helpers;
using BookingKampus.Models;
using Microsoft.Data.SqlClient;

namespace BookingKampus.Repositories;

public static class UserRepository
{
    static User Map(SqlDataReader r) => new()
    {
        UserId = r.GetInt32(0),
        Username = r.GetString(1),
        Nama = r.GetString(2),
        Role = r.GetString(3),
        IsActive = r.GetBoolean(4),
        PasswordHash = r.GetString(5)
    };

    public static async Task<User> GetByUsernameAsync(string username)
    {
        var list = await Db.QueryAsync<User>(
            "SELECT UserId, Username, Nama, [Role], IsActive, PasswordHash FROM dbo.Users WHERE Username = @u",
            Map, Db.P("@u", username));
        return list.FirstOrDefault();
    }

    public static Task<int> CountAsync() => Db.ScalarAsync<int>("SELECT COUNT(*) FROM dbo.Users");

    /// <summary>Dipakai saat seed akun demo, sebelum ada pengguna yang login.</summary>
    public static Task SeedAsync(string username, string passwordHash, string nama, string role) =>
        Db.ExecAsync(
            "INSERT INTO dbo.Users (Username, PasswordHash, Nama, [Role], IsActive) VALUES (@u, @h, @n, @r, 1)",
            Db.P("@u", username), Db.P("@h", passwordHash), Db.P("@n", nama), Db.P("@r", role));
}
