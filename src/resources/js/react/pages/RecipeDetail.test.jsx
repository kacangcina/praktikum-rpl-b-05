import { fireEvent, render, screen } from '@testing-library/react';
import { MemoryRouter, Route, Routes } from 'react-router-dom';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { api } from '../api.js';
import RecipeDetail from './RecipeDetail.jsx';

vi.mock('../api.js', () => ({ api: vi.fn() }));
vi.mock('../context/AuthContext.jsx', () => ({
    useAuth: () => ({ user: { id: 2, username: 'viewer' } }),
}));

const recipe = {
    id: 1,
    title: 'Ayam Goreng',
    description: 'Ayam gurih.',
    difficulty: 'mudah',
    estimated_time: 30,
    thumbnail_url: null,
    creator: { id: 1, username: 'chef', can_upload_videos: true },
    tools: [],
    ingredients: [],
    steps: [],
    reviews: [],
    my_review: null,
    is_saved: false,
    can_edit_recipe: false,
    can_delete: false,
    video: {
        title: 'Teknik Menggoreng Ayam',
        description: 'Gunakan api sedang.',
        url: '/recipes/1/video/watch',
    },
};

describe('RecipeDetail offline video handling', () => {
    beforeEach(() => {
        api.mockResolvedValue({ recipe });
    });

    it('pauses video and keeps the page open when the connection drops', async () => {
        const pause = vi.spyOn(HTMLMediaElement.prototype, 'pause').mockImplementation(() => {});

        render(
            <MemoryRouter initialEntries={['/recipes/1']}>
                <Routes>
                    <Route path="/recipes/:id" element={<RecipeDetail />} />
                </Routes>
            </MemoryRouter>,
        );

        expect(await screen.findByText('Teknik Menggoreng Ayam')).toBeInTheDocument();
        fireEvent.offline(window);

        expect(await screen.findByText('Koneksi terputus. Periksa koneksi internet kamu.')).toBeInTheDocument();
        expect(pause).toHaveBeenCalled();
        expect(screen.getByText('Ayam Goreng')).toBeInTheDocument();
    });

    it('shows the CuBu placeholder when the thumbnail cannot be loaded', async () => {
        api.mockResolvedValue({
            recipe: {
                ...recipe,
                video: null,
                thumbnail_url: '/storage/recipe-thumbnails/missing.jpg',
            },
        });

        render(
            <MemoryRouter initialEntries={['/recipes/1']}>
                <Routes>
                    <Route path="/recipes/:id" element={<RecipeDetail />} />
                </Routes>
            </MemoryRouter>,
        );

        fireEvent.error(await screen.findByAltText('Ayam Goreng'));

        expect(screen.queryByAltText('Ayam Goreng')).not.toBeInTheDocument();
        expect(screen.getByText('CuBu')).toBeInTheDocument();
    });
});
