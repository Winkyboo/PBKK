using Microsoft.Data.SqlClient;

namespace BookingKampus.Helpers;

/// <summary>Akses ADO.NET. Semua query memakai parameter, tidak ada penggabungan string dari input pengguna.</summary>
public static class Db
{
    public static SqlConnection Create() => new SqlConnection(Config.ConnectionString);

    public static SqlParameter P(string name, object value) => new SqlParameter(name, value ?? DBNull.Value);

    public static async Task<List<T>> QueryAsync<T>(string sql, Func<SqlDataReader, T> map, params SqlParameter[] ps)
    {
        await using var c = Create();
        await c.OpenAsync();
        await using var cmd = new SqlCommand(sql, c);
        cmd.Parameters.AddRange(ps);
        await using var r = await cmd.ExecuteReaderAsync();
        var list = new List<T>();
        while (await r.ReadAsync()) list.Add(map(r));
        return list;
    }

    public static async Task<int> ExecAsync(string sql, params SqlParameter[] ps)
    {
        await using var c = Create();
        await c.OpenAsync();
        await using var cmd = new SqlCommand(sql, c);
        cmd.Parameters.AddRange(ps);
        return await cmd.ExecuteNonQueryAsync();
    }

    public static async Task<T> ScalarAsync<T>(string sql, params SqlParameter[] ps)
    {
        await using var c = Create();
        await c.OpenAsync();
        await using var cmd = new SqlCommand(sql, c);
        cmd.Parameters.AddRange(ps);
        var o = await cmd.ExecuteScalarAsync();
        if (o == null || o == DBNull.Value) return default;
        return (T)Convert.ChangeType(o, typeof(T));
    }
}
