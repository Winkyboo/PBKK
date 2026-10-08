using System;
using System.Collections.Generic;
using System.Linq;
using System.Windows;
using System.Windows.Controls;
using StudentRegistration.Models;
using StudentRegistration.Repositories;

namespace StudentRegistration;

public partial class MainWindow : Window
{
    private readonly StudentRepository _studentRepository;
    private List<Student> _allStudents = new();

    public MainWindow()
    {
        InitializeComponent();
        _studentRepository = new StudentRepository();
        LoadStudents();
    }

    private void LoadStudents()
    {
        try
        {
            // Ambil data dari SQL Server menggunakan StudentRepository
            _allStudents = _studentRepository.GetAll();

            // Tampilkan ke DataGrid & perbarui panel statistik
            ApplyFilterAndDisplay();
            UpdateStatistics();
        }
        catch (Exception ex)
        {
            MessageBox.Show(
                $"Gagal memuat data dari SQL Server:\n{ex.Message}",
                "Database Error",
                MessageBoxButton.OK,
                MessageBoxImage.Error);
        }
    }

    private void UpdateStatistics()
    {
        // Hitung statistik secara real-time
        int total = _allStudents.Count;
        int tiCount = _allStudents.Count(s => s.ProgramName.Contains("Teknik Industri", StringComparison.OrdinalIgnoreCase) || s.ProgramName.Equals("TI", StringComparison.OrdinalIgnoreCase));
        int siCount = _allStudents.Count(s => s.ProgramName.Contains("Sistem Informasi", StringComparison.OrdinalIgnoreCase) || s.ProgramName.Equals("SI", StringComparison.OrdinalIgnoreCase));
        int tcCount = _allStudents.Count(s => s.ProgramName.Contains("Teknik Informatika", StringComparison.OrdinalIgnoreCase) || s.ProgramName.Equals("TC", StringComparison.OrdinalIgnoreCase));
        int aktCount = _allStudents.Count(s => s.ProgramName.Contains("Aktuaria", StringComparison.OrdinalIgnoreCase) || s.ProgramName.Equals("Akt", StringComparison.OrdinalIgnoreCase));

        TxtTotalStudents.Text = total.ToString();
        TxtTotalTC.Text = tcCount.ToString();
        TxtTotalSI.Text = siCount.ToString();
        TxtTotalAkt.Text = aktCount.ToString();
        TxtTotalTI.Text = tiCount.ToString();
    }

    private void ApplyFilterAndDisplay()
    {
        string query = TxtSearch.Text.Trim();

        var filteredList = string.IsNullOrWhiteSpace(query)
            ? _allStudents
            : _allStudents.Where(s =>
                s.NRP.Contains(query, StringComparison.OrdinalIgnoreCase) ||
                s.Name.Contains(query, StringComparison.OrdinalIgnoreCase) ||
                s.ProgramName.Contains(query, StringComparison.OrdinalIgnoreCase) ||
                s.Address.Contains(query, StringComparison.OrdinalIgnoreCase)
            ).ToList();

        StudentDataGrid.ItemsSource = filteredList;
        TxtStatusInfo.Text = $"Menampilkan {filteredList.Count} dari {_allStudents.Count} Data";
    }

    private void TxtSearch_TextChanged(object sender, TextChangedEventArgs e)
    {
        ApplyFilterAndDisplay();
    }

    private void RefreshButton_Click(object sender, RoutedEventArgs e)
    {
        TxtSearch.Text = string.Empty;
        LoadStudents();
    }
}