using System.Collections.ObjectModel;
using BookingKampus.Helpers;
using BookingKampus.Models;
using BookingKampus.Repositories;
using BookingKampus.Services;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;

namespace BookingKampus.ViewModels;

public partial class ApprovalViewModel : ViewModelBase
{
    public ObservableCollection<Booking> Pending { get; } = new();

    [ObservableProperty] [NotifyPropertyChangedFor(nameof(HasSelection))] private Booking selected;
    [ObservableProperty] private string catatan = "";
    [ObservableProperty] private string fasilitasText = "";
    [ObservableProperty] private bool isEmpty;

    public bool HasSelection => Selected != null;

    public override async Task LoadAsync()
    {
        Session.Require(Roles.Admin);
        Pending.ReplaceWith(await BookingRepository.ListPendingAsync());
        IsEmpty = Pending.Count == 0;
        Selected = null;
    }

    partial void OnSelectedChanged(Booking value)
    {
        Catatan = "";
        FasilitasText = "";
        if (value == null) return;
        _ = RunAsync(async () =>
        {
            var names = await BookingRepository.FasilitasNamesAsync(value.BookingId);
            FasilitasText = names.Count == 0 ? "Tidak ada fasilitas tambahan dipilih." : string.Join(", ", names);
        });
    }

    [RelayCommand]
    private async Task ApproveAsync()
    {
        if (Selected == null) return;
        int id = Selected.BookingId;
        string note = Catatan;
        await RunAsync(async () =>
        {
            await BookingService.ApproveAsync(id, note);
            await LoadAsync();
        }, "Booking disetujui dan pemohon diberi tahu.");
    }

    [RelayCommand]
    private async Task RejectAsync()
    {
        if (Selected == null) return;
        int id = Selected.BookingId;
        string note = Catatan;
        await RunAsync(async () =>
        {
            await BookingService.RejectAsync(id, note);
            await LoadAsync();
        }, "Booking ditolak dan pemohon diberi tahu.");
    }
}
