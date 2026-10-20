using BookingKampus.Helpers;
using BookingKampus.Services;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using Microsoft.Data.SqlClient;

namespace BookingKampus.ViewModels;

public partial class LoginViewModel : ObservableObject
{
    [ObservableProperty] private string username = "";
    [ObservableProperty] private string password = "";
    [ObservableProperty] [NotifyPropertyChangedFor(nameof(HasError))] private string errorMessage;
    [ObservableProperty] private bool isBusy;

    public bool HasError => !string.IsNullOrEmpty(ErrorMessage);
    public event Action LoggedIn;

    [RelayCommand]
    private async Task LoginAsync()
    {
        ErrorMessage = null;
        if (string.IsNullOrWhiteSpace(Username) || string.IsNullOrEmpty(Password))
        {
            ErrorMessage = "Username dan kata sandi wajib diisi.";
            return;
        }

        bool ok = false;
        try
        {
            IsBusy = true;
            await AuthService.LoginAsync(Username.Trim(), Password);
            ok = true;
        }
        catch (AppException ex) { ErrorMessage = ex.Message; }
        catch (SqlException ex)
        {
            Logger.Write(ex);
            ErrorMessage = "Tidak dapat terhubung ke database. Periksa SQL Server dan appsettings.json.";
        }
        catch (Exception ex)
        {
            Logger.Write(ex);
            ErrorMessage = "Terjadi kesalahan yang tidak terduga. Detail sudah dicatat di log.";
        }
        finally { IsBusy = false; }

        if (ok) LoggedIn?.Invoke();
    }
}
