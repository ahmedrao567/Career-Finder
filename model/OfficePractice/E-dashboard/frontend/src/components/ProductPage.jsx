import React, { useEffect, useState } from "react";
import { authFetch } from "../apis/authfetch";

export default function ProductPage() {
  const [categories, setCategories] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState("");
  const [products, setProducts] = useState([]);
  const [newProduct, setNewProduct] = useState({
    title: "",
    price: "",
    categoryIds: [],
  });

  const [isModalOpen, setIsModalOpen] = useState(false);
  const [page, setPage] = useState(1);
  const [fresh, setFresh] = useState(false);

  const limit = 5
  const userId = 1;

  
  useEffect(() => {
    const fetchCategories = async () => {
      try {
        const res = await authFetch("http://localhost:3000/api/categories");
        const data = await res.json();
        setCategories(data);
      } catch (err) {
        console.error(err);
      }
    };
    fetchCategories();
  }, []);

  
  useEffect(() => {
    const fetchProducts = async () => {
      try {
        let url = `http://localhost:3000/api/products?page=${page}&limit=${limit}`;

        if (selectedCategory) {
          url += `&categoryId=${selectedCategory}`;
        }

        const res = await authFetch(url);
        const data = await res.json();
        setProducts(data);
      } catch (err) {
        console.error(err);
      }
    };

    fetchProducts();
  }, [page, selectedCategory, fresh]);

  
  const handleCategoryChange = (e) => {
    setSelectedCategory(e.target.value);
    setPage(1);
  };

  const handleAddProduct = async (e) => {
    e.preventDefault();

    if (!newProduct.title || !newProduct.price || newProduct.categoryIds.length === 0) {
      alert("Fill all fields and select at least one category!");
      return;
    }

    try {

      const res = await authFetch("http://localhost:3000/api/products", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          title: newProduct.title,
          price: parseFloat(newProduct.price),
          userId,
          categoryIds: newProduct.categoryIds.map(Number),
        }),
      });

      const addedProduct = await res.json();
      setProducts((prev) => [addedProduct, ...prev]);
      setNewProduct({ title: "", price: "", categoryIds: [] });
      setFresh((prev) => !prev);
      setIsModalOpen(false);
    } catch (err) {
      console.error(err);
    }
  };

  const toggleCategory = (id) => {
    setNewProduct((prev) => ({
      ...prev,
      categoryIds: prev.categoryIds.includes(id)
        ? prev.categoryIds.filter((cid) => cid !== id)
        : [...prev.categoryIds, id],
    }));
  };

  return (
    <div className="p-6 bg-gray-50 min-h-screen">
      <h1 className="text-2xl font-bold mb-6">🛒 Products</h1>

      <div className="flex gap-4 mb-6">
        <select
          className="border rounded px-3 py-2"
          value={selectedCategory}
          onChange={handleCategoryChange}
        >
          <option value="">All Categories</option>
          {categories.map((cat) => (
            <option key={cat.id} value={cat.id}>
              {cat.title}
            </option>
          ))}
        </select>

        <button
          onClick={() => setIsModalOpen(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          + Add Product
        </button>
      </div>

      <div className="bg-white shadow rounded overflow-hidden">
        <table className="w-full text-left">
          <thead className="bg-gray-100">
            <tr>
              <th className="px-4 py-3">ID</th>
              <th className="px-4 py-3">Title</th>
              <th className="px-4 py-3">Price</th>
              <th className="px-4 py-3">Categories</th>
              <th className="px-4 py-3">Actions</th>
            </tr>
          </thead>

          <tbody>
            {products.length === 0 ? (
              <tr>
                <td colSpan="5" className="text-center py-6 text-gray-500">
                  No products found 🫠
                </td>
              </tr>
            ) : (
              products.map((prod) => (
                <tr key={prod.id} className="border-t">
                  <td className="px-4 py-3">{prod.id}</td>
                  <td className="px-4 py-3 font-medium">{prod.title}</td>
                  <td className="px-4 py-3 text-green-600 font-semibold">
                    ${prod.price}
                  </td>
                  <td className="px-4 py-3">
                    {prod.Categories?.length
                      ? prod.Categories.map((c) => c.title).join(", ")
                      : "-"}
                  </td>
                  <td className="px-4 py-3 flex gap-2">
                    <button className="px-3 py-1 bg-red-500 text-white rounded">
                      Delete
                    </button>
                    <button className="px-3 py-1 bg-yellow-400 text-white rounded">
                      Edit
                    </button>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>

       
        <div className="flex justify-center items-center gap-4 p-4">
          <button
            onClick={() => setPage((p) => p - 1)}
            disabled={page === 1}
            className="px-4 py-2 bg-gray-300 rounded disabled:opacity-50"
          >
            Prev
          </button>

          <span className="font-semibold">Page {page}</span>

          <button
            onClick={() => setPage((p) => p + 1)}
            disabled={products.length < limit}
            className="px-4 py-2 bg-gray-300 rounded disabled:opacity-50"
          >
            Next
          </button>
        </div>
      </div>

     
      {isModalOpen && (
        <div className="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center">
          <div className="bg-white rounded-lg w-96 p-6">
            <h2 className="text-xl font-bold mb-4">Add Product</h2>

            <form onSubmit={handleAddProduct} className="flex flex-col gap-3">
              <input
                type="text"
                placeholder="Product title"
                className="border rounded px-3 py-2"
                value={newProduct.title}
                onChange={(e) =>
                  setNewProduct((p) => ({ ...p, title: e.target.value }))
                }
              />

              <input
                type="number"
                placeholder="Price"
                className="border rounded px-3 py-2"
                value={newProduct.price}
                onChange={(e) =>
                  setNewProduct((p) => ({ ...p, price: e.target.value }))
                }
              />

              <div className="border rounded p-2 max-h-40 overflow-y-auto">
                {categories.map((cat) => (
                  <label key={cat.id} className="flex gap-2">
                    <input
                      type="checkbox"
                      checked={newProduct.categoryIds.includes(cat.id)}
                      onChange={() => toggleCategory(cat.id)}
                    />
                    {cat.title}
                  </label>
                ))}
              </div>

              <div className="flex justify-end gap-2">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="px-4 py-2 bg-gray-300 rounded"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="px-4 py-2 bg-blue-600 text-white rounded"
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
