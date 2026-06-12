<?php
session_start();

// Otorisasi Web: Jika tidak ada session login admin, tolak akses dan lempar ke login.php
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require 'koneksi.php';

// ==========================================
// PROSES 1: CREATE (Tambah Data)
// ==========================================
if (isset($_POST['tambah'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    
    $conn->query("INSERT INTO konten_olahraga (judul, kategori, deskripsi) VALUES ('$judul', '$kategori', '$deskripsi')");
    header("Location: index.php");
    exit();
}

// ==========================================
// PROSES 2: UPDATE (Ubah Data)
// ==========================================
if (isset($_POST['ubah'])) {
    $id = intval($_POST['id']);
    $judul = $conn->real_escape_string($_POST['judul']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    
    $conn->query("UPDATE konten_olahraga SET judul='$judul', kategori='$kategori', deskripsi='$deskripsi' WHERE id=$id");
    header("Location: index.php");
    exit();
}

// ==========================================
// PROSES 3: DELETE (Hapus Data)
// ==========================================
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM konten_olahraga WHERE id=$id");
    header("Location: index.php");
    exit();
}

// Menangkap data lama untuk ditampilkan di form edit jika tombol "Edit" diklik
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM konten_olahraga WHERE id=$id_edit");
    if ($res && $res->num_rows > 0) {
        $edit_data = $res->fetch_assoc();
    }
}

// ==========================================
// PROSES 4: READ (Mengambil data untuk Tabel)
// ==========================================
$konten_list = $conn->query("SELECT * FROM konten_olahraga ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Konten Olahraga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
        }
        .main-card {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-top: 40px;
            margin-bottom: 40px;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .table td.text-left {
            text-align: left;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-8">
                
                <div class="main-card">
                    <h3 class="text-center text-uppercase fw-bold mb-4" style="letter-spacing: 1px; color: #333;">
                        CRUD KONTEN OLAHRAGA
                    </h3>
                    
                    <div class="d-flex justify-content-between align-items: center; border-bottom pb-2 mb-4">
                        <span class="text-muted small">Log masuk sebagai: <strong class="text-dark"><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                        <a href="logout.php" class="text-danger fw-bold text-decoration-none small" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Logout</a>
                    </div>

                    <h6 class="fw-bold mb-3 text-secondary"><?php echo $edit_data ? 'Ubah Data Konten' : 'Tambah Data Konten'; ?></h6>

                    <form method="POST" action="">
                        <?php if ($edit_data): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Judul Konten</label>
                            <input type="text" name="judul" class="form-control form-control-sm" value="<?php echo $edit_data ? htmlspecialchars($edit_data['judul']) : ''; ?>" required autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Kategori Cabang Olahraga</label>
                            <input type="text" name="kategori" class="form-control form-control-sm" placeholder="Contoh: Sepakbola, Badminton" value="<?php echo $edit_data ? htmlspecialchars($edit_data['kategori']) : ''; ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Deskripsi Konten</label>
                            <textarea name="deskripsi" class="form-control form-control-sm" rows="4" required><?php echo $edit_data ? htmlspecialchars($edit_data['deskripsi']) : ''; ?></textarea>
                        </div>

                        <div class="d-grid gap-2 mb-5">
                            <?php if ($edit_data): ?>
                                <button type="submit" name="ubah" class="btn btn-primary btn-sm fw-bold">Simpan Perubahan</button>
                                <a href="index.php" class="btn btn-secondary btn-sm fw-bold">Batal</a>
                            <?php else: ?>
                                <button type="submit" name="tambah" class="btn btn-success btn-sm fw-bold">Simpan</button>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm mt-3">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 8%;">ID</th>
                                    <th style="width: 25%;">Judul Konten</th>
                                    <th style="width: 17%;">Kategori</th>
                                    <th style="width: 32%;">Deskripsi</th>
                                    <th style="width: 18%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                if ($konten_list && $konten_list->num_rows > 0):
                                    while($row = $konten_list->fetch_assoc()): 
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td class="text-left"><strong><?php echo htmlspecialchars($row['judul']); ?></strong></td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1"><?php echo htmlspecialchars($row['kategori']); ?></span></td>
                                    <td class="text-left"><?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?></td>
                                    <td>
                                        <a href="index.php?edit=<?php echo $row['id']; ?>" class="btn btn-link btn-sm text-primary p-0 m-0 fw-bold text-decoration-none">Edit</a>
                                        <span class="text-muted">|</span>
                                        <a href="index.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus konten ini?')" class="btn btn-link btn-sm text-danger p-0 m-0 fw-bold text-decoration-none">Hapus</a>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile; 
                                else:
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3 small">Belum ada data konten olahraga tersedia.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>