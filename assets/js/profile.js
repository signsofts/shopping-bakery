function toggleEditForm() {
    const form = document.getElementById('editForm');
    const profile = document.getElementById('profile');
    if (form.style.display === 'none') {
        form.style.display = 'block';
        profile.style.display = 'none';
        
    } else {
        form.style.display = 'none';
        profile.style.display = 'block';
    }
}
