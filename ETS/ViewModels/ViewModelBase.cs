using BookingKampus.Helpers;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using Microsoft.Data.SqlClient;

namespace BookingKampus.ViewModels;

public abstract partial class ViewModelBase : ObservableObject
{
    [ObservableProperty]
    [NotifyPropertyChangedFor(nameof(HasError))]
    private string errorMessage;

    [ObservableProperty]
    private bool isBusy;

    public bool HasError => !string.IsNullOrEmpty(ErrorMessage);

    public IAsyncRelayCommand LoadCommand { get; }

    protected ViewModelBase()
    {
        LoadCommand = new AsyncRelayCommand(() => RunAsync(LoadAsync));
    }

    public virtual Task LoadAsync() => Task.CompletedTask;

    /// <summary>Menjalankan aksi dengan state loading dan penanganan error yang seragam. Aplikasi tidak crash karena error data.</summary>
    protected async Task RunAsync(Func<Task> action, string successMessage = null)
    {
        try
        {
            IsBusy = true;
            ErrorMessage = null;
            await action();
            if (successMessage != null) Toast.Show(successMessage);
        }
        catch (AppException ex) { Toast.Show(ex.Message, true); }
        catch (UnauthorizedAccessException ex) { Toast.Show(ex.Message, true); }
        catch (SqlException ex) when (ex.Number is 2627 or 2601)
        {
            Toast.Show("Data dengan kode, nama, atau username yang sama sudah ada.", true);
        }
        catch (SqlException ex) when (ex.Number == 547)
        {
            Toast.Show("Data ini masih dipakai oleh data lain (misalnya booking), sehingga tidak dapat dihapus.", true);
        }
        catch (SqlException ex)
        {
            Logger.Write(ex);
            ErrorMessage = "Tidak dapat mengakses database. Periksa koneksi SQL Server dan pengaturan di appsettings.json.";
            Toast.Show(ErrorMessage, true);
        }
        catch (Exception ex)
        {
            Logger.Write(ex);
            ErrorMessage = "Terjadi kesalahan yang tidak terduga. Detail teknis sudah dicatat di log.";
            Toast.Show(ErrorMessage, true);
        }
        finally
        {
            IsBusy = false;
        }
    }
}
