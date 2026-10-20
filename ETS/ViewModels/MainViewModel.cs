using System.Windows.Threading;
using BookingKampus.Helpers;
using BookingKampus.Models;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;

namespace BookingKampus.ViewModels;

public class NavItem
{
    public NavItem(string title, Func<ViewModelBase> factory) { Title = title; Factory = factory; }
    public string Title { get; }
    public Func<ViewModelBase> Factory { get; }
}

public partial class MainViewModel : ObservableObject, IDisposable
{
    readonly DispatcherTimer _toastTimer = new() { Interval = TimeSpan.FromSeconds(4) };

    public List<NavItem> NavItems { get; } = new();
    public string UserName => Session.Current.Nama;
    public string RoleText => Roles.Display(Session.Current.Role);
    public event Action LoggedOut;

    [ObservableProperty] private NavItem selectedNav;
    [ObservableProperty] private ViewModelBase currentViewModel;
    [ObservableProperty] private string toastText = "";
    [ObservableProperty] private bool isToastVisible;
    [ObservableProperty] private bool isToastError;

    public MainViewModel()
    {
        NavItems.Add(new NavItem("Ringkasan", () => new DashboardViewModel()));
        NavItems.Add(new NavItem("Booking", () => new BookingViewModel()));
        if (Session.InRole(Roles.Admin)) NavItems.Add(new NavItem("Persetujuan", () => new ApprovalViewModel()));
        NavItems.Add(new NavItem("Ruangan", () => new RuanganViewModel()));
        NavItems.Add(new NavItem("Fasilitas", () => new FasilitasViewModel()));

        Toast.Raised += OnToast;
        _toastTimer.Tick += (_, _) => { _toastTimer.Stop(); IsToastVisible = false; };

        SelectedNav = NavItems[0];
    }

    partial void OnSelectedNavChanged(NavItem value)
    {
        if (value == null) return;
        var vm = value.Factory();
        CurrentViewModel = vm;
        vm.LoadCommand.Execute(null);
    }

    void OnToast(string message, bool isError)
    {
        ToastText = message;
        IsToastError = isError;
        IsToastVisible = true;
        _toastTimer.Stop();
        _toastTimer.Start();
    }

    [RelayCommand]
    private void Logout() => LoggedOut?.Invoke();

    public void Dispose()
    {
        Toast.Raised -= OnToast;
        _toastTimer.Stop();
    }
}
