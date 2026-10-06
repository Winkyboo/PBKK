using System;
using System.Collections.Generic;
using Microsoft.Data.SqlClient;
using StudentManager.Models;

namespace StudentManager.Data;

public class StudentRepository
{
    private readonly string connectionString =
        @"Server=localhost;Database=StudentDB;Trusted_Connection=True;TrustServerCertificate=True;";

    public List<Student> GetAll()
    {
        var students = new List<Student>();

        using SqlConnection connection = new(connectionString);
        string sql = "SELECT Id, NRP, Nama, Prodi, Gender, Email FROM Students ORDER BY Id DESC";

        using SqlCommand command = new(sql, connection);
        connection.Open();

        using SqlDataReader reader = command.ExecuteReader();
        while (reader.Read())
        {
            students.Add(new Student
            {
                Id = Convert.ToInt32(reader["Id"]),
                NRP = reader["NRP"].ToString()!,
                Nama = reader["Nama"].ToString()!,
                Prodi = reader["Prodi"].ToString()!,
                Gender = reader["Gender"].ToString()!,
                Email = reader["Email"].ToString()!
            });
        }

        return students;
    }

    public void Insert(Student student)
    {
        using SqlConnection connection = new(connectionString);
        string sql = "INSERT INTO Students (NRP, Nama, Prodi, Gender, Email) VALUES (@NRP, @Nama, @Prodi, @Gender, @Email)";

        using SqlCommand command = new(sql, connection);
        command.Parameters.AddWithValue("@NRP", student.NRP);
        command.Parameters.AddWithValue("@Nama", student.Nama);
        command.Parameters.AddWithValue("@Prodi", student.Prodi);
        command.Parameters.AddWithValue("@Gender", student.Gender);
        command.Parameters.AddWithValue("@Email", student.Email);

        connection.Open();
        command.ExecuteNonQuery();
    }

    public void Update(Student student)
    {
        using SqlConnection connection = new(connectionString);
        string sql = "UPDATE Students SET NRP=@NRP, Nama=@Nama, Prodi=@Prodi, Gender=@Gender, Email=@Email WHERE Id=@Id";

        using SqlCommand command = new(sql, connection);
        command.Parameters.AddWithValue("@Id", student.Id);
        command.Parameters.AddWithValue("@NRP", student.NRP);
        command.Parameters.AddWithValue("@Nama", student.Nama);
        command.Parameters.AddWithValue("@Prodi", student.Prodi);
        command.Parameters.AddWithValue("@Gender", student.Gender);
        command.Parameters.AddWithValue("@Email", student.Email);

        connection.Open();
        command.ExecuteNonQuery();
    }

    public void Delete(int id)
    {
        using SqlConnection connection = new(connectionString);
        using SqlCommand command = new("DELETE FROM Students WHERE Id=@Id", connection);

        command.Parameters.AddWithValue("@Id", id);

        connection.Open();
        command.ExecuteNonQuery();
    }

    public Student? GetById(int id)
    {
        using SqlConnection connection = new(connectionString);
        string sql = "SELECT Id, NRP, Nama, Prodi, Gender, Email FROM Students WHERE Id = @Id";
    
        using SqlCommand command = new(sql, connection);
        command.Parameters.AddWithValue("@Id", id);
    
        connection.Open();
    
        using SqlDataReader reader = command.ExecuteReader();
        if (reader.Read())
        {
            return new Student
            {
                Id = Convert.ToInt32(reader["Id"]),
                NRP = reader["NRP"].ToString()!,
                Nama = reader["Nama"].ToString()!,
                Prodi = reader["Prodi"].ToString()!,
                Gender = reader["Gender"].ToString()!,
                Email = reader["Email"].ToString()!
            };
        }
    
        return null;
    }
    
    public bool IsNrpExists(string nrp)
    {
        using SqlConnection connection = new(connectionString);
        string sql = "SELECT COUNT(*) FROM Students WHERE NRP = @NRP";
    
        using SqlCommand command = new(sql, connection);
        command.Parameters.AddWithValue("@NRP", nrp);
    
        connection.Open();
        return Convert.ToInt32(command.ExecuteScalar()) > 0;
    }
    
    public bool IsNrpExistsForOtherStudent(string nrp, int currentId)
    {
        using SqlConnection connection = new(connectionString);
        string sql = "SELECT COUNT(*) FROM Students WHERE NRP = @NRP AND Id <> @CurrentId";
    
        using SqlCommand command = new(sql, connection);
        command.Parameters.AddWithValue("@NRP", nrp);
        command.Parameters.AddWithValue("@CurrentId", currentId);
    
        connection.Open();
        return Convert.ToInt32(command.ExecuteScalar()) > 0;
    }

    public List<Student> Search(string keyword)
    {
        var students = new List<Student>();

        using SqlConnection connection = new(connectionString);
        string sql = "SELECT Id, NRP, Nama, Prodi, Gender, Email FROM Students " +
                     "WHERE NRP LIKE @Keyword OR Nama LIKE @Keyword OR Prodi LIKE @Keyword ORDER BY Id DESC";

        using SqlCommand command = new(sql, connection);
        command.Parameters.AddWithValue("@Keyword", "%" + keyword + "%");

        connection.Open();

        using SqlDataReader reader = command.ExecuteReader();
        while (reader.Read())
        {
            students.Add(new Student
            {
                Id = Convert.ToInt32(reader["Id"]),
                NRP = reader["NRP"].ToString()!,
                Nama = reader["Nama"].ToString()!,
                Prodi = reader["Prodi"].ToString()!,
                Gender = reader["Gender"].ToString()!,
                Email = reader["Email"].ToString()!
            });
        }

        return students;
    }
}