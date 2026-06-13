import React, { useState } from "react";
import Categories from "./Categories";
import ProductPage from "./ProductPage";

export default function Dashboard() {
  const [activeTab, setActiveTab] = useState("Dashboard");

  const tabs = ["Dashboard", "Categories", "Products"];

  return (
    <div className="flex h-screen bg-gray-100">
      
      <aside className="w-64 bg-white shadow-md flex flex-col">
        <div className="p-6 text-2xl font-bold border-b border-gray-200 text-gray-800">
          My Dashboard
        </div>
        <nav className="mt-6 flex-1">
          <ul>
            {tabs.map((tab) => (
              <li
                key={tab}
                onClick={() => setActiveTab(tab)}
                className={`px-6 py-3 mb-1 rounded cursor-pointer hover:bg-blue-100 transition 
                  ${activeTab === tab ? "bg-blue-500 text-white font-semibold" : "text-gray-700"}`}
              >
                {tab}
              </li>
            ))}
          </ul>
        </nav>
      </aside>

      
      <main className="flex-1 p-8 overflow-auto">
        {activeTab === "Dashboard" && (
          <div className="bg-white shadow rounded-lg p-6">
            <h1 className="text-3xl font-bold mb-4 text-gray-800">
              Welcome to the Dashboard
            </h1>
            <p className="text-gray-600">
              Here is your main dashboard content. You can manage categories,
              products, and view reports from here.
            </p>
          </div>
        )}

        {activeTab === "Categories" && <Categories />}

        {activeTab === "Products" && <ProductPage />}
      </main>
    </div>
  );
}
