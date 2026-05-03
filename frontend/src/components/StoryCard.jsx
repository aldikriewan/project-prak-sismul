import React from "react";

const StoryCard = ({ title, author, excerpt }) => {
  return (
    <div className="max-w-sm rounded overflow-hidden shadow-lg bg-white p-4">
      <div className="font-bold text-xl mb-2">{title}</div>
      <p className="text-gray-700 text-base">
        {excerpt}...
      </p>
      <div className="text-gray-500 text-sm mt-2">Written by {author}</div>
    </div>
  );
};

export default StoryCard;
