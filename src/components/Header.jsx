import React from "react";
import { Link } from "react-router-dom";

const Header = () => {
  return (
    <header class="flex items-center justify-between px-6 py-4 bg-white shadow border-b-2 h-[80px]">

    <div class="flex items-center pl-[20px]">
    <img src="/public/lamp.png" alt="Lamp Icon" width="24" height="24" />
      <span class="text-gray-700 font-medium text-xl ml-2">Light's on</span>
    </div>

    <div class="flex items-center w-1/2 ">
      <input 
        type="text" 
        placeholder="Cari Buku" 
        class="w-full px-4 py-2 border-t border-b border-l border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm text-gray-700"
      />
      <button class="px-4 py-2 border border-gray-300 rounded-r-lg bg-white">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 text-gray-500">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l5.5 5.5M10 10a6 6 0 1112 0 6 6 0 01-12 0z" />
        </svg>
      </button>
    </div>

    <div class="flex items-center space-x-2 pr-[20px]">
      <p className="text-gray-700 font-medium text-xl">Read on with</p>
      <p class="text-yellow-500 font-bold text-xl"> Light's on</p>
    </div>
  </header>
  );
};

export default Header;
