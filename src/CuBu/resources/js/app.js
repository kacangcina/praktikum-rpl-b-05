import {
    BadgeCheck,
    ArrowRight,
    Bookmark,
    BookmarkCheck,
    BookmarkX,
    BookOpen,
    ChefHat,
    CirclePlus,
    CircleX,
    Clock,
    Download,
    House,
    LogOut,
    Pencil,
    Play,
    Plus,
    RotateCcw,
    Replace,
    Save,
    Search,
    Send,
    Settings,
    ShieldCheck,
    Trash2,
    Upload,
    Video,
    createIcons,
} from 'lucide';

const updateStepNumbers = () => {
    document.querySelectorAll('[data-numbered]').forEach((list) => {
        list.querySelectorAll('.step-editor-number').forEach((number, index) => {
            number.textContent = index + 1;
        });
    });
};

document.addEventListener('click', (event) => {
    const addButton = event.target.closest('[data-add-row]');

    if (addButton) {
        const listId = addButton.dataset.addRow;
        const list = document.getElementById(listId);
        const template = document.getElementById(`${listId}-template`);

        if (list && template) {
            list.append(template.content.cloneNode(true));
            list.lastElementChild?.querySelector('input, textarea')?.focus();
            updateStepNumbers();
        }
    }

    const removeButton = event.target.closest('[data-remove-row]');

    if (removeButton) {
        const list = removeButton.closest('[data-row-list]');
        const rows = list?.children.length ?? 0;

        if (rows > 1) {
            removeButton.parentElement.remove();
            updateStepNumbers();
        } else {
            const fields = removeButton.parentElement.querySelectorAll('input, textarea');
            fields.forEach((field) => {
                field.value = '';
            });
            fields[0]?.focus();
        }
    }
});

const imageInput = document.querySelector('[data-image-input]');
const imagePreview = document.querySelector('[data-image-preview]');
const imageDropzone = document.querySelector('[data-image-dropzone]');
const videoInput = document.querySelector('[data-video-input]');
const videoDropzone = document.querySelector('[data-video-dropzone]');
const videoFileName = document.querySelector('[data-video-file-name]');

imageInput?.addEventListener('change', () => {
    const [file] = imageInput.files;

    if (!file || !imagePreview || !imageDropzone) {
        return;
    }

    imagePreview.src = URL.createObjectURL(file);
    imageDropzone.classList.add('has-preview');
});

videoInput?.addEventListener('change', () => {
    const [file] = videoInput.files;

    if (!file || !videoDropzone || !videoFileName) {
        return;
    }

    videoDropzone.classList.add('has-file');
    videoFileName.textContent = file.name;
});

const avatarInput = document.querySelector('[data-avatar-input]');
const avatarPreview = document.querySelector('[data-avatar-preview]');
const avatarInitials = document.querySelector('[data-avatar-initials]');

avatarInput?.addEventListener('change', () => {
    const [file] = avatarInput.files;

    if (!file || !avatarPreview) {
        return;
    }

    avatarPreview.src = URL.createObjectURL(file);
    avatarPreview.style.display = 'block';

    if (avatarInitials) {
        avatarInitials.style.display = 'none';
    }
});

updateStepNumbers();

createIcons({
    icons: {
        BadgeCheck,
        ArrowRight,
        Bookmark,
        BookmarkCheck,
        BookmarkX,
        BookOpen,
        ChefHat,
        CirclePlus,
        CircleX,
        Clock,
        Download,
        House,
        LogOut,
        Pencil,
        Play,
        Plus,
        RotateCcw,
        Replace,
        Save,
        Search,
        Send,
        Settings,
        ShieldCheck,
        Trash2,
        Upload,
        Video,
    },
    attrs: {
        'aria-hidden': 'true',
        'stroke-width': 2,
    },
});
