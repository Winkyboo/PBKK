namespace BookingKampus.Helpers;

/// <summary>Mencatat error teknis ke file log, tanpa menampilkan detail ke pengguna.</summary>
public static class Logger
{
    static readonly string Folder = Path.Combine(
        Environment.GetFolderPath(Environment.SpecialFolder.ApplicationData), "BookingKampus");

    public static void Write(Exception ex)
    {
        try
        {
            Directory.CreateDirectory(Folder);
            File.AppendAllText(Path.Combine(Folder, "error.log"),
                $"[{DateTime.Now:yyyy-MM-dd HH:mm:ss}] {ex.GetType().Name}: {ex.Message}{Environment.NewLine}{ex.StackTrace}{Environment.NewLine}{Environment.NewLine}");
        }
        catch
        {
            // Gagal menulis log tidak boleh menjatuhkan aplikasi.
        }
    }
}
