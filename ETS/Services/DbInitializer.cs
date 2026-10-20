using System.Reflection;
using System.Text.RegularExpressions;
using BookingKampus.Helpers;
using BookingKampus.Models;
using BookingKampus.Repositories;
using Microsoft.Data.SqlClient;

namespace BookingKampus.Services;

/// <summary>Membuat database, tabel, data contoh, dan akun demo saat aplikasi pertama kali dijalankan.</summary>
public static class DbInitializer
{
    public static async Task EnsureAsync()
    {
        var builder = new SqlConnectionStringBuilder(Config.ConnectionString);
        var dbName = builder.InitialCatalog;
        builder.InitialCatalog = "master";

        await using (var master = new SqlConnection(builder.ConnectionString))
        {
            await master.OpenAsync();
            await using var create = new SqlCommand(
                @"IF DB_ID(@n) IS NULL
                  BEGIN
                      DECLARE @sql NVARCHAR(300) = N'CREATE DATABASE ' + QUOTENAME(@n);
                      EXEC (@sql);
                  END", master);
            create.Parameters.AddWithValue("@n", dbName);
            await create.ExecuteNonQueryAsync();
        }

        var asm = Assembly.GetExecutingAssembly();
        var resource = asm.GetManifestResourceNames()
            .First(n => n.EndsWith("schema.sql", StringComparison.OrdinalIgnoreCase));
        string script;
        using (var stream = asm.GetManifestResourceStream(resource))
        using (var reader = new StreamReader(stream))
            script = await reader.ReadToEndAsync();

        var batches = Regex.Split(script, @"^\s*GO\s*$", RegexOptions.Multiline | RegexOptions.IgnoreCase);
        await using var conn = Db.Create();
        await conn.OpenAsync();
        foreach (var batch in batches)
        {
            if (string.IsNullOrWhiteSpace(batch)) continue;
            await using var cmd = new SqlCommand(batch, conn);
            await cmd.ExecuteNonQueryAsync();
        }

        if (await UserRepository.CountAsync() == 0)
        {
            await UserRepository.SeedAsync("admin", AuthService.Hash("Admin123!"), "Administrator Kampus", Roles.Admin);
            await UserRepository.SeedAsync("mahasiswa", AuthService.Hash("Mahasiswa123!"), "Mahasiswa Contoh", Roles.Pemohon);
        }
    }
}
