import React from "react";
import { Link } from "react-router-dom";

const Footer = () => {
  return (
    <footer className="bg-gray-200 text-gray-800 py-6">
      <div className="text-xs flex justify-center space-x-2 mb-4">Follow us on:  
      </div>
      <div className="max-w-6xl mx-auto px-4 text-center">
        <ul className="flex justify-center space-x-6 mb-4">
          <li>
            <a
              href="https://facebook.com"
              target="_blank"
              rel="noopener noreferrer"
              className="text-xs flex items-center space-x-2 hover:text-blue-500 transition"
            >
              <i className="bi bi-facebook text-lg"></i>
              <span>Facebook</span>
            </a>
          </li>
          <li>
            <a
              href="https://instagram.com"
              target="_blank"
              rel="noopener noreferrer"
              className="text-xs flex items-center space-x-2 hover:text-pink-500 transition"
            >
              <i className="bi bi-instagram text-lg"></i>
              <span>Instagram</span>
            </a>
          </li>
          <li>
            <a
              href="https://x.com/"
              target="_blank"
              rel="noopener noreferrer"
              className="text-xs flex items-center space-x-2 hover:text-blue-400 transition"
            >
              <i className="bi bi-twitter-x text-lg"></i>
              <span>Twitter</span>
            </a>
          </li>
        </ul>
        <p className="text-xs mt-8">
          &copy; {new Date().getFullYear()} Light's on. All rights reserved.
        </p>
      </div>
    </footer>
  );
};

export default Footer;
