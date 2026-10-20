using System.Collections.ObjectModel;
using BookingKampus.Helpers;
using BookingKampus.Models;
using BookingKampus.Repositories;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;

namespace BookingKampus.ViewModels;

public partial class FasilitasViewModel : ViewModelBase
{
    public ObservableCollection<Fasilitas> Items { get; } = new();

    [ObservableProperty] private string searchText = "";
    [ObservableProperty] private Fasilitas selected;
    [ObservableProperty] private bool isEmpty;
    [ObservableProperty] private string nama = "";
    [ObservableProperty] private string deskripsi = "";
    [ObservableProperty] private bool isActive = true;
    [ObservableProperty] private string editorTitle = "Fasilitas baru";

    int _id;

    public bool CanEdit => Session.InRole(Roles.Admin);
    public bool IsViewOnly => !CanEdit;

    public override async Task LoadAsync()
    {
        await LoadItemsAsync();
        ResetForm();
    }

    async Task LoadItemsAsync()
    {
        Items.ReplaceWith(await FasilitasRepository.ListAsync(SearchText));
        IsEmpty = Items.Count == 0;
    }

    partial void OnSearchTextChanged(string value) => _ = RunAsync(LoadItemsAsync);

    partial void OnSelectedChanged(Fasilitas value)
    {
        if (value == null) return;
        _id = value.FasilitasId;
        Nama = value.Nama; Deskripsi = value.Deskripsi; IsActive = value.IsActive;
        EditorTitle = $"Ubah fasilitas {value.Nama}";
    }

    void ResetForm()
    {
        _id = 0; Nama = ""; Deskripsi = ""; IsActive = true; EditorTitle = "Fasilitas baru";
    }

    [RelayCommand]
    private void NewItem()
    {
        Selected = null;
        ResetForm();
    }

    [RelayCommand]
    private async Task SaveAsync()
    {
        await RunAsync(async () =>
        {
            Session.Require(Roles.Admin);
            var namaV = (Nama ?? "").Trim();
            if (namaV.Length == 0 || namaV.Length > 100) throw new AppException("Nama fasilitas wajib diisi, maksimal 100 karakter.");
            if ((Deskripsi ?? "").Length > 200) throw new AppException("Deskripsi maksimal 200 karakter.");
            await FasilitasRepository.SaveAsync(new Fasilitas { FasilitasId = _id, Nama = namaV, Deskripsi = (Deskripsi ?? "").Trim(), IsActive = IsActive });
            await LoadItemsAsync();
            Selected = null;
            ResetForm();
        }, "Data fasilitas disimpan.");
    }

    [RelayCommand]
    private async Task DeleteAsync()
    {
        if (_id == 0) { Toast.Show("Pilih fasilitas yang akan dihapus dari tabel.", true); return; }
        if (!Dialogs.Confirm($"Hapus fasilitas {Nama}? Tindakan ini tidak dapat dibatalkan.")) return;
        await RunAsync(async () =>
        {
            Session.Require(Roles.Admin);
            await FasilitasRepository.DeleteAsync(_id);
            await LoadItemsAsync();
            Selected = null;
            ResetForm();
        }, "Fasilitas dihapus.");
    }
}
