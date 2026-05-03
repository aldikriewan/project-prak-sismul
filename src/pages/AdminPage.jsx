import React, { useState, useEffect } from "react";
import axios from "axios";

const AdminPage = () => {
  const [title, setTitle] = useState("");
  const [author, setAuthor] = useState("");
  const [image, setImage] = useState(null);
  const [backgroundImage, setBackgroundImage] = useState(null);
  const [description, setDescription] = useState("");
  const [storyDetail, setStoryDetail] = useState("");
  const [stories, setStories] = useState([]);
  const [editingId, setEditingId] = useState(null); // Untuk menentukan apakah sedang mengedit

  useEffect(() => {
    fetchStories();
  }, []);

  const fetchStories = async () => {
    try {
      const response = await axios.get("http://localhost:8000/api/stori");
      setStories(response.data.data);
    } catch (error) {
      console.error("Error fetching stories:", error);
      alert("Terjadi kesalahan saat mengambil data cerita.");
    }
  };

  const handleImageChange = (e) => {
    setImage(e.target.files[0]);
  };

  const handleBackgroundImageChange = (e) => {
    setBackgroundImage(e.target.files[0]);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    const formData = new FormData();
    formData.append("title", title);
    formData.append("author", author);
    if (image) formData.append("image", image);
    if (backgroundImage) formData.append("background_image", backgroundImage);
    formData.append("description", description);
    formData.append("story_detail", storyDetail);

    try {
      if (editingId) {
        // Edit cerita
        await axios.put(`http://localhost:8000/api/stori/${editingId}`, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });
        alert("Cerita berhasil diperbarui!");
      } else {
        // Tambahkan cerita baru
        await axios.post("http://localhost:8000/api/stori", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });
        alert("Cerita berhasil ditambahkan!");
      }

      resetForm();
      fetchStories();
    } catch (error) {
      console.error("Error submitting story:", error);
      alert("Terjadi kesalahan saat menyimpan cerita.");
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm("Yakin ingin menghapus cerita ini?")) {
      try {
        await axios.delete(`http://localhost:8000/api/stori/${id}`);
        alert("Cerita berhasil dihapus!");
        fetchStories();
      } catch (error) {
        console.error("Error deleting story:", error);
        alert("Terjadi kesalahan saat menghapus cerita.");
      }
    }
  };

  const handleEdit = (story) => {
    setEditingId(story.id);
    setTitle(story.title);
    setAuthor(story.author);
    setDescription(story.description);
    setStoryDetail(story.story_detail);
    setImage(null); // Reset gambar karena tidak ditampilkan saat edit
    setBackgroundImage(null);
  };

  const resetForm = () => {
    setEditingId(null);
    setTitle("");
    setAuthor("");
    setImage(null);
    setBackgroundImage(null);
    setDescription("");
    setStoryDetail("");
  };

  return (
    <div className="container mx-auto p-4">
      <h1 className="text-2xl font-bold mb-6">Admin - Tambah dan Kelola Cerita</h1>
      <div className="flex flex-wrap lg:flex-nowrap gap-6">
        {/* Bagian Form */}
        <div className="w-full lg:w-1/2 bg-white p-6 shadow-md rounded-md">
          <h2 className="text-xl font-bold mb-4">{editingId ? "Edit Cerita" : "Tambah Cerita"}</h2>
          <form onSubmit={handleSubmit}>
            <div className="mb-4">
              <label className="block font-semibold mb-2">Judul</label>
              <input
                type="text"
                className="border border-gray-300 p-2 w-full rounded-md"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                required
              />
            </div>
            <div className="mb-4">
              <label className="block font-semibold mb-2">Penulis</label>
              <input
                type="text"
                className="border border-gray-300 p-2 w-full rounded-md"
                value={author}
                onChange={(e) => setAuthor(e.target.value)}
                required
              />
            </div>
            <div className="mb-4">
              <label className="block font-semibold mb-2">Gambar</label>
              <input
                type="file"
                className="border border-gray-300 p-2 w-full rounded-md"
                onChange={handleImageChange}
              />
            </div>
            <div className="mb-4">
              <label className="block font-semibold mb-2">Gambar Latar Belakang</label>
              <input
                type="file"
                className="border border-gray-300 p-2 w-full rounded-md"
                onChange={handleBackgroundImageChange}
              />
            </div>
            <div className="mb-4">
              <label className="block font-semibold mb-2">Deskripsi</label>
              <textarea
                className="border border-gray-300 p-2 w-full rounded-md"
                rows="3"
                value={description}
                onChange={(e) => setDescription(e.target.value)}
                required
              />
            </div>
            <div className="mb-4">
              <label className="block font-semibold mb-2">Detail Cerita</label>
              <textarea
                className="border border-gray-300 p-2 w-full rounded-md"
                rows="5"
                value={storyDetail}
                onChange={(e) => setStoryDetail(e.target.value)}
                required
              />
            </div>
            <button type="submit" className="bg-blue-500 text-white py-2 px-4 rounded-md">
              {editingId ? "Simpan Perubahan" : "Tambahkan Cerita"}
            </button>
          </form>
        </div>

        {/* Bagian Daftar Cerita */}
        <div className="w-full lg:w-1/2 bg-gray-100 p-6 shadow-md rounded-md">
          <h2 className="text-xl font-bold mb-4">Daftar Cerita</h2>
          {stories.map((story) => (
            <div key={story.id} className="border-b pb-4">
              <h3 className="font-semibold text-lg">{story.title}</h3>
              <button onClick={() => handleEdit(story)} className="text-blue-500 mr-2">
                Edit
              </button>
              <button onClick={() => handleDelete(story.id)} className="text-red-500">
                Hapus
              </button>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default AdminPage;
