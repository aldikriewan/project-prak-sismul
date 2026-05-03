import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import HomePage from "./pages/HomePage";
import AdminPage from "./pages/AdminPage";
import StoryDetailPage from "./pages/StoryDetailPage";
import Header from "./components/Header";
import Footer from "./components/Footer";


function App() {
    return (
        <Router>
            <Header />
            <Routes>
                <Route path="/" element={<HomePage />} />
                <Route path="/admin" element={<AdminPage />} />
                <Route path="/story/:id" element={<StoryDetailPage />} />
            </Routes>
            <Footer />
        </Router>
    );
}

export default App;
