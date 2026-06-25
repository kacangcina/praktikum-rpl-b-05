import { fireEvent, render, screen, waitFor } from '@testing-library/react';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import FileUploadField from './FileUploadField.jsx';

describe('FileUploadField', () => {
    beforeEach(() => {
        URL.createObjectURL = vi.fn(() => 'blob:preview-file');
        URL.revokeObjectURL = vi.fn();
    });

    it('shows an image preview and can cancel the selected file', async () => {
        render(<FileUploadField name="thumbnail" label="Foto thumbnail" kind="image" />);
        const input = screen.getByLabelText('Foto thumbnail');
        const file = new File(['image'], 'resep.jpg', { type: 'image/jpeg' });

        fireEvent.change(input, { target: { files: [file] } });

        expect(await screen.findByAltText('Preview resep.jpg')).toHaveAttribute('src', 'blob:preview-file');
        expect(screen.getByText('resep.jpg')).toBeInTheDocument();

        fireEvent.click(screen.getByRole('button', { name: 'Batalkan pilihan resep.jpg' }));

        expect(screen.queryByText('resep.jpg')).not.toBeInTheDocument();
        expect(screen.getByLabelText('Foto thumbnail')).not.toBe(input);
        expect(screen.getByLabelText('Foto thumbnail').files).toHaveLength(0);
        await waitFor(() => expect(URL.revokeObjectURL).toHaveBeenCalledWith('blob:preview-file'));
    });

    it('shows document metadata without creating an object URL', async () => {
        render(<FileUploadField name="document" label="Dokumen KTP" kind="document" />);
        const file = new File(['pdf-content'], 'identitas.pdf', { type: 'application/pdf' });

        fireEvent.change(screen.getByLabelText('Dokumen KTP'), { target: { files: [file] } });

        expect(await screen.findByText('Dokumen siap diunggah')).toBeInTheDocument();
        expect(screen.getByText('identitas.pdf')).toBeInTheDocument();
        expect(URL.createObjectURL).not.toHaveBeenCalled();
    });
});
