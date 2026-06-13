import React, { useState, useEffect } from "react";
import { useForm } from "react-hook-form";
import { authFetch } from "../apis/authfetch";

export default function Categories() {
  const [categories, setCategories] = useState([]);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [page, setPage] = useState(1);
  const [fresh, setFresh] = useState(false);
  const limit = 5;
  const {
    register,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm();

  useEffect(() => {
    fetchCategories();
  }, [page, fresh]);

  const fetchCategories = async () => {
    try {
      const res = await authFetch(
        `http://localhost:3000/api/categories?page=${page}&limit=${limit}`
      );
      const data = await res.json();
      setCategories(data);
    } catch (err) {
      console.error("Failed to fetch categories:", err);
    }
  };

  const openModal = () => setIsModalOpen(true);
  const closeModal = () => {
    setIsModalOpen(false);
    reset();
  };

  const onSubmit = async (data) => {
    try {
      const res = await authFetch("http://localhost:3000/api/categories/add", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });
      const updatedCategories = await res.json();
      setCategories((prev) => [...prev, updatedCategories]);
      setFresh((prev) => !prev);
      closeModal();
    } catch (err) {
      console.error("Failed to add category:", err);
    }
  };

  const handleDelete = async (id) => {
    try {
      await authFetch(`http://localhost:3000/api/delcategory/${id}`, {
        method: "DELETE",
        body: JSON.stringify({ id }),
        headers: { "Content-Type": "application/json" },
      });
      setCategories(categories.filter((cat) => cat.id !== id));
    } catch (err) {
      console.error("Failed to delete category:", err);
    }
  };

  const handleEdit = async (cat) => {
    const newTitle = prompt("Enter new category title:", cat.title);
      const res = await authFetch(
        `http://localhost:3000/api/patchcategory/${cat.id}`,
        {
          method: "PATCH",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ title: newTitle }),
        }
      );
      const updatedCategory = await res.json();
      setCategories(categories.map((c) => (c.id === cat.id ? updatedCategory : c)))
  };


  return (
    <div className=" bg-gray-100 p-6">

      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold text-gray-800">Categories</h1>
        <button
          onClick={openModal}
          className="bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition"
        >
          + Add Category
        </button>
      </div>


      <div className="bg-white shadow rounded-lg overflow-hidden">
        <table className="min-w-full text-left border-collapse">
          <thead className="bg-gray-200">
            <tr>
              <th className="px-6 py-3 font-medium text-gray-700">ID</th>
              <th className="px-6 py-3 font-medium text-gray-700">Name</th>
              <th className="px-6 py-3 font-medium text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            {categories.length === 0 ? (
              <tr>
                <td colSpan="3" className="text-center py-6 text-gray-400">
                  No categories yet.
                </td>
              </tr>
            ) : (
              categories.map((cat) => (
                <tr key={cat.id} className="border-b hover:bg-gray-50 transition">
                  <td className="px-6 py-4">{cat.id}</td>
                  <td className="px-6 py-4">{cat.title}</td>
                  <td className="px-6 py-4 flex gap-2">
                    <button className="px-3 py-1 bg-yellow-300 rounded hover:bg-yellow-400 transition"
                      onClick={() => handleEdit(cat)}
                    >
                      Edit
                    </button>
                    <button
                      onClick={() => handleDelete(cat.id)}
                      className="px-3 py-1 bg-red-400 text-white rounded hover:bg-red-600 transition"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>


        <div className="flex justify-center items-center gap-4 p-4">
          <button
            onClick={() => setPage(page - 1)}
            disabled={page === 1}
            className="px-4 py-2 bg-gray-300 rounded disabled:opacity-50 hover:bg-gray-400 transition"
          >
            Prev
          </button>
          <span className="font-semibold">Page {page}</span>
          <button
            onClick={() => setPage(page + 1)}
            disabled={categories.length < limit}
            className="px-4 py-2 bg-gray-300 rounded disabled:opacity-50 hover:bg-gray-400 transition"
          >
            Next
          </button>
        </div>
      </div>


      {isModalOpen && (
        <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg shadow-lg w-96 p-6">
            <h2 className="text-xl font-bold mb-4 text-gray-800">Add Category</h2>
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
              <div>
                <label className="block mb-1 font-medium text-gray-700">Title</label>
                <input
                  type="text"
                  {...register("title", { required: "Title is required" })}
                  className="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                {errors.title && (
                  <span className="text-red-500 text-sm">{errors.title.message}</span>
                )}
              </div>
              <div className="flex justify-end gap-2">
                <button
                  type="button"
                  onClick={closeModal}
                  className="px-4 py-2 rounded border hover:bg-gray-100 transition"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition"
                >
                  Add
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
