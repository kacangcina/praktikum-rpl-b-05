import { fireEvent, render, screen } from '@testing-library/react';
import { MemoryRouter, Route, Routes } from 'react-router-dom';
import { describe, expect, it, vi } from 'vitest';
import { api } from '../api.js';
import Profile from './Profile.jsx';

vi.mock('../api.js', () => ({ api: vi.fn() }));
vi.mock('../context/AuthContext.jsx', () => ({
    useAuth: () => ({ user: { id: 5 }, refresh: vi.fn() }),
}));

describe('Profile', () => {
    it('loads the profile without returning a Promise as effect cleanup', async () => {
        api.mockResolvedValue({
            profile: {
                id: 5,
                name: 'Wibi',
                username: 'wibi',
                bio: null,
                avatar_url: null,
                role_label: 'User',
                is_admin: false,
                can_upload_videos: false,
                can_publish_recipes: true,
            },
            recipes: [],
            is_owner: true,
            notifications: [],
            unread_notifications_count: 0,
            latest_verification: null,
        });

        render(
            <MemoryRouter initialEntries={['/profile/5']}>
                <Routes>
                    <Route path="/profile/:id" element={<Profile />} />
                </Routes>
            </MemoryRouter>,
        );

        expect(await screen.findByText('Wibi')).toBeInTheDocument();
        expect(screen.getByText('Belum ada resep yang dipublikasikan.')).toBeInTheDocument();
    });

    it('keeps a long notification list collapsed until the user opens it', async () => {
        api.mockResolvedValue({
            profile: {
                id: 5,
                name: 'Wibi',
                username: 'wibi',
                bio: null,
                avatar_url: null,
                role_label: 'User',
                is_admin: false,
                can_upload_videos: false,
                can_publish_recipes: true,
            },
            recipes: [],
            is_owner: true,
            notifications: Array.from({ length: 5 }, (_, index) => ({
                id: String(index),
                level: 'info',
                title: `Notifikasi ${index + 1}`,
                message: `Pesan ${index + 1}`,
                created_at: '2026-06-09T10:00:00Z',
                read_at: null,
            })),
            unread_notifications_count: 5,
            latest_verification: null,
        });

        render(
            <MemoryRouter initialEntries={['/profile/5']}>
                <Routes>
                    <Route path="/profile/:id" element={<Profile />} />
                </Routes>
            </MemoryRouter>,
        );

        const toggle = await screen.findByRole('button', { name: /Pusat aktivitas/i });
        expect(screen.queryByText('Pesan 1')).not.toBeInTheDocument();

        fireEvent.click(toggle);

        expect(await screen.findByText('Pesan 1')).toBeInTheDocument();
        expect(screen.queryByText('Pesan 4')).not.toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Lihat semua (5)' })).toBeInTheDocument();
    });
});
