import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";

const StoryDetailPage = () => {
  const { id } = useParams();
  const [story, setStory] = useState(null);

  useEffect(() => {
    fetch(`http://localhost/php-backend/api.php?action=getStory&id=${id}`)
      .then((response) => response.json())
      .then((data) => setStory(data));
  }, [id]);

  if (!story) return <p>Loading...</p>;

  return (
    <div className="container mx-auto p-4">
      {/* Hero Section */}
      <div className="bg-blue-500 text-white p-6 rounded-lg shadow-md mb-8">
        <h1 className="text-4xl font-bold">{story.title}</h1>
        <p className="text-lg mt-2">Written by <strong>{story.author}</strong></p>
        <p className="text-sm text-gray-300 mt-1">Published on {new Date(story.published_at).toLocaleDateString()}</p>
      </div>

      {/* Story Content */}
      <div className="bg-white rounded-lg shadow-lg p-6">
        <div className="mb-6">
          <img src={story.image_url} alt="Story cover" className="w-full rounded-lg" />
        </div>
        <p className="text-gray-700 mb-4">{story.content}</p>
      </div>

      {/* Additional Section - Comments, etc. */}
      <div className="mt-12 bg-gray-100 p-6 rounded-lg shadow-md">
        <h2 className="text-2xl font-bold mb-4">Comments</h2>
        <p className="text-gray-700">Be the first to comment on this story!</p>
        {/* Form for adding comments or showing existing comments */}
        <div className="mt-4">
          <textarea className="w-full p-4 border border-gray-300 rounded-md" placeholder="Write your comment..."></textarea>
          <button className="bg-blue-500 text-white p-2 mt-4 rounded-md">Post Comment</button>
        </div>
      </div>
    </div>
  );
};

export default StoryDetailPage;
