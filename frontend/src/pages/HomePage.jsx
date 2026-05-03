import React, { useEffect, useRef, useState } from "react";
import { Link } from "react-router-dom";
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";

const HomePage = () => {
  const [stori, setStoris] = useState([]);
        const [loading, setLoading] = useState(true);

        const fetchStoris = async () => {
                setLoading(true);
                try {
                        const response = await fetch('http://127.0.0.1:8000/api/stori');
                        const result = await response.json();
                        if (result.success) {
                                setStoris(result.data);
                        }
                } catch (error) {
                        console.error('Error fetching data:', error);
                } finally {
                        setLoading(false);
                }
        };

        useEffect(() => {
                fetchStoris();
      }, []);

  // Data cerita trending dan cerita lainnya


  // Slider settings
  const sliderSettings = {
    dots: true,
    infinite: true,
    speed: 500,
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 3000,
  };

  // For drag-to-scroll functionality
  const scrollContainerRef = useRef(null);

  const handleDragStart = (e) => {
    const scrollContainer = scrollContainerRef.current;
    scrollContainer.isDragging = true;
    scrollContainer.initialX = e.clientX;
    scrollContainer.scrollLeft = scrollContainer.scrollLeft;
  };

  const handleDragEnd = () => {
    const scrollContainer = scrollContainerRef.current;
    scrollContainer.isDragging = false;
  };

  const handleDragMove = (e) => {
    const scrollContainer = scrollContainerRef.current;
    if (!scrollContainer.isDragging) return;
    const deltaX = e.clientX - scrollContainer.initialX;
    scrollContainer.scrollLeft = scrollContainer.scrollLeft - deltaX;
    scrollContainer.initialX = e.clientX;
  };

  return (
    <div className="bg-white-100 min-h-screen">

      {/* Cerita Trending */}
      <div className="mb-12">
        <Slider {...sliderSettings}>
            {stori.map((stori, index) => (
            <div key={index} className="relative">
              <div
                className="bg-cover bg-center w-full h-[683px] relative"
                style={{ backgroundImage: `url(http://127.0.0.1:8000/storage/${stori.background_image})` }}
              >
                <div className="absolute inset-0 bg-black bg-opacity-30 flex flex-col justify-center items-center text-center text-white">
                  <h2 className="text-3xl font-bold mb-[10px] pt-[20px]">{stori.title}</h2>
                  <p className="text-lg">{stori.author}</p>
                  <p className="text-lg mt-[480px] mb-[50px]">{stori.description}</p>
                </div>
                <div className="absolute inset-0 flex justify-center items-center">
                  <img
                    src={`http://127.0.0.1:8000/storage/${stori.image}`}
                    alt={stori.title}
                    className="w-[300px] h-[400px] object-cover rounded-lg shadow-lg"
                  />
                </div>
              </div>
            </div>
          ))}
        </Slider>
      </div>

      {/* Daftar Semua Cerita */}
      <div className="max-w-7xl mx-auto py-8 pb-[80px]">
        <h2 className="text-2xl font-medium text-xl text-gray-800 mb-6">Cerita Terkini</h2>
        <div className="relative">
          <div
            ref={scrollContainerRef}
            className="flex space-x-4 overflow-x-auto scrollbar-hide cursor-grab"
            onMouseDown={handleDragStart}
            onMouseUp={handleDragEnd}
            onMouseMove={handleDragMove}
          >
            {stori.map((stori, index) => (
              <div
                key={index}
                className="bg-white shadow-md border-md border-b border-t rounded-lg p-4 w-64 h-[420px] flex-shrink-0 relative"
              >
                <img
                  src={`http://127.0.0.1:8000/storage/${stori.image}`} // Placeholder default
                  alt={stori.title}
                  className="rounded-md mb-4 h-[283px] w-full object-cover"
                />
                <h4 className="font-semibold text-gray-800">{stori.title}</h4>
                <p className="text-gray-500">{stori.author}</p>

                {/* Baca Selengkapnya link */}
                <div className="absolute bottom-4 right-4">
                </div>
              </div>
            ))}
          </div>
        </div>
        </div>
    </div>
  );
};

export default HomePage;
