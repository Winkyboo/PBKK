namespace StudentManager.Models;

public class Student
{
    public int Id { get; set; }
    public string NRP { get; set; } = string.Empty;
    public string Nama { get; set; } = string.Empty;
    public string Prodi { get; set; } = string.Empty;
    public string Gender { get; set; } = string.Empty;
    public string Email { get; set; } = string.Empty;
}