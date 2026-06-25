import { fireEvent, render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';
import { api } from '../api.js';
import CookingConsultation from './CookingConsultation.jsx';

vi.mock('../api.js', () => ({
    api: vi.fn(),
    firstError: () => null,
}));

describe('CookingConsultation', () => {
    it('shows the documented rejection for an out-of-scope question', async () => {
        api.mockResolvedValue({
            in_scope: false,
            answer: 'Maaf, saya hanya dapat membantu pertanyaan seputar memasak dan resep.',
        });

        render(<CookingConsultation />);

        fireEvent.change(screen.getByLabelText('Pertanyaan konsultasi memasak'), {
            target: { value: 'Siapa presiden negara lain?' },
        });
        fireEvent.click(screen.getByRole('button', { name: 'Kirim' }));

        expect(await screen.findByText('Maaf, saya hanya dapat membantu pertanyaan seputar memasak dan resep.')).toBeInTheDocument();
        expect(api).toHaveBeenCalledWith('/api/cooking-consultation', {
            method: 'POST',
            body: { question: 'Siapa presiden negara lain?' },
        });
    });
});
