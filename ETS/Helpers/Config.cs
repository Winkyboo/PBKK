using System.Text.Json;

namespace BookingKampus.Helpers;

public static class Config
{
    public static string ConnectionString { get; private set; } = "";

    public static void Load()
    {
        var path = Path.Combine(AppContext.BaseDirectory, "appsettings.json");
        using var doc = JsonDocument.Parse(File.ReadAllText(path));
        ConnectionString = doc.RootElement
            .GetProperty("ConnectionStrings")
            .GetProperty("Default")
            .GetString() ?? "";
    }
}
