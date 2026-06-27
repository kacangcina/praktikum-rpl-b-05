import { Navigate, Route, Routes } from 'react-router-dom';
import Layout from './components/Layout.jsx';
import AdminLayout from './components/AdminLayout.jsx';
import ProtectedRoute from './components/ProtectedRoute.jsx';
import { useAuth } from './context/AuthContext.jsx';
import AdminVerificationDetail from './pages/AdminVerificationDetail.jsx';
import AdminVerifications from './pages/AdminVerifications.jsx';
import AdminAiSettings from './pages/AdminAiSettings.jsx';
import AdminRecipes from './pages/AdminRecipes.jsx';
import AdminUsers from './pages/AdminUsers.jsx';
import Collection from './pages/Collection.jsx';
import CookingConsultation from './pages/CookingConsultation.jsx';
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
                    <Route path="/consultation" element={<CookingConsultation />} />
                    <Route path="/creator/apply" element={<CreatorApply />} />
                    <Route path="/admin" element={<AdminLayout />}>
                        <Route index element={<Navigate to="creator-verifications" replace />} />
                        <Route path="creator-verifications" element={<AdminVerifications />} />
                        <Route path="creator-verifications/:id" element={<AdminVerificationDetail />} />
                        <Route path="ai-settings" element={<AdminAiSettings />} />
                        <Route path="users" element={<AdminUsers />} />
                        <Route path="recipes" element={<AdminRecipes />} />
                    </Route>
                </Route>

                <Route path="*" element={<Navigate to="/" replace />} />
            </Route>
        </Routes>
    );
}
