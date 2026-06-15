import { Navigate, Route, Routes } from 'react-router-dom';
import Layout from './components/Layout.jsx';
import ProtectedRoute from './components/ProtectedRoute.jsx';
import { useAuth } from './context/AuthContext.jsx';
import AdminVerificationDetail from './pages/AdminVerificationDetail.jsx';
import AdminVerifications from './pages/AdminVerifications.jsx';
import Collection from './pages/Collection.jsx';
import CreatorApply from './pages/CreatorApply.jsx';
import Home from './pages/Home.jsx';
import Login from './pages/Login.jsx';
import Profile from './pages/Profile.jsx';
import ProfileEdit from './pages/ProfileEdit.jsx';
import RecipeDetail from './pages/RecipeDetail.jsx';
import RecipeForm from './pages/RecipeForm.jsx';
import Register from './pages/Register.jsx';
import VideoForm from './pages/VideoForm.jsx';

function MyProfile() {
    const { user } = useAuth();

    return user ? <Navigate to={`/profile/${user.id}`} replace /> : <Navigate to="/login" replace />;
}

export default function App() {
    return (
        <Routes>
            <Route element={<Layout />}>
                <Route path="/" element={<Home />} />
                <Route path="/recipes" element={<Home />} />
                <Route path="/recipes/:id" element={<RecipeDetail />} />
                <Route path="/profile/:id" element={<Profile />} />
                <Route path="/login" element={<Login />} />
                <Route path="/register" element={<Register />} />

                <Route element={<ProtectedRoute />}>
                    <Route path="/profile" element={<MyProfile />} />
                    <Route path="/profile/edit" element={<ProfileEdit />} />
                    <Route path="/recipes/create" element={<RecipeForm />} />
                    <Route path="/recipes/:id/edit" element={<RecipeForm />} />
                    <Route path="/recipes/:id/video" element={<VideoForm />} />
                    <Route path="/collections" element={<Collection />} />
                    <Route path="/creator/apply" element={<CreatorApply />} />
                    <Route path="/admin/creator-verifications" element={<AdminVerifications />} />
                    <Route path="/admin/creator-verifications/:id" element={<AdminVerificationDetail />} />
                </Route>

                <Route path="*" element={<Navigate to="/" replace />} />
            </Route>
        </Routes>
    );
}
