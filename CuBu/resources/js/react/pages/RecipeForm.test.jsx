import { fireEvent, render, screen, waitFor } from '@testing-library/react';
import { MemoryRouter } from 'react-router-dom';
import { describe, expect, it, vi } from 'vitest';
import { api } from '../api.js';
import RecipeForm from './RecipeForm.jsx';

vi.mock('../api.js', () => ({ api: vi.fn() }));
vi.mock('../context/AuthContext.jsx', () => ({
    useAuth: () => ({
        user: { id: 1, can_publish_recipes: true, can_upload_videos: false },
    }),
}));

describe('RecipeForm validation placement', () => {
    it('renders an ingredient error inside the ingredient row', async () => {
        api.mockRejectedValue({
            message: 'Data tidak valid.',
            errors: { 'ingredient_names.0': ['Bahan wajib diisi.'] },
        });

        render(
            <MemoryRouter>
                <RecipeForm />
            </MemoryRouter>,
        );

        fireEvent.click(screen.getByRole('button', { name: 'Publikasikan' }));

        const error = await screen.findByText('Bahan wajib diisi.');
        const ingredientInput = screen.getByLabelText('Nama Bahan');

        expect(ingredientInput.parentElement.parentElement).toContainElement(error);
        expect(screen.queryByText('Data tidak valid.')).not.toBeInTheDocument();
        await waitFor(() => expect(api).toHaveBeenCalled());
    });
});
