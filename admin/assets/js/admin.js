// ===================================
// Admin Panel JavaScript
// ===================================

// Admin Dark Mode Toggle
document.addEventListener('DOMContentLoaded', function() {
    const adminThemeToggle = document.getElementById('admin-theme-toggle');
    if (adminThemeToggle) {
        // Check for saved theme
        const currentAdminTheme = localStorage.getItem('adminTheme') || 'light';
        if (currentAdminTheme === 'dark') {
            document.body.classList.add('admin-dark-mode');
            const icon = adminThemeToggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }
        }
        
        adminThemeToggle.addEventListener('click', () => {
            document.body.classList.toggle('admin-dark-mode');
            const isDark = document.body.classList.contains('admin-dark-mode');
            const icon = adminThemeToggle.querySelector('i');
            
            if (isDark) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
                localStorage.setItem('adminTheme', 'dark');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
                localStorage.setItem('adminTheme', 'light');
            }
        });
    }
});

// Mobile sidebar toggle
const mobileToggle = document.getElementById('mobile-toggle');
const sidebar = document.querySelector('.sidebar');

if (mobileToggle) {
    mobileToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 1024) {
        if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    }
});

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Close modal when clicking outside
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
});

// Confirm delete
function confirmDelete(message = 'Bu öğeyi silmek istediğinize emin misiniz?') {
    return confirm(message);
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        animation: slideInRight 0.3s ease;
        min-width: 300px;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Image preview
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Auto-save form
function autoSave(formId, interval = 30000) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    setInterval(() => {
        const formData = new FormData(form);
        formData.append('auto_save', '1');
        
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Form otomatik kaydedildi');
            }
        })
        .catch(error => console.error('Auto-save error:', error));
    }, interval);
}

// Color picker change
document.querySelectorAll('input[type="color"]').forEach(input => {
    input.addEventListener('change', (e) => {
        const preview = e.target.nextElementSibling;
        if (preview && preview.classList.contains('color-preview')) {
            preview.style.background = e.target.value;
        }
    });
});

// Sortable lists (drag & drop)
function initSortable(listClass) {
    const lists = document.querySelectorAll(listClass);
    lists.forEach(list => {
        let draggedItem = null;
        
        list.querySelectorAll('.sortable-item').forEach(item => {
            item.draggable = true;
            
            item.addEventListener('dragstart', (e) => {
                draggedItem = item;
                e.dataTransfer.effectAllowed = 'move';
                item.classList.add('dragging');
            });
            
            item.addEventListener('dragend', () => {
                item.classList.remove('dragging');
            });
            
            item.addEventListener('dragover', (e) => {
                e.preventDefault();
                const afterElement = getDragAfterElement(list, e.clientY);
                if (afterElement == null) {
                    list.appendChild(draggedItem);
                } else {
                    list.insertBefore(draggedItem, afterElement);
                }
            });
        });
    });
}

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.sortable-item:not(.dragging)')];
    
    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

// Character counter
document.querySelectorAll('textarea[maxlength]').forEach(textarea => {
    const maxLength = textarea.getAttribute('maxlength');
    const counter = document.createElement('div');
    counter.className = 'char-counter';
    counter.style.cssText = 'text-align: right; font-size: 12px; color: #6b7280; margin-top: 4px;';
    
    const updateCounter = () => {
        const remaining = maxLength - textarea.value.length;
        counter.textContent = `${remaining} karakter kaldı`;
    };
    
    textarea.parentNode.appendChild(counter);
    textarea.addEventListener('input', updateCounter);
    updateCounter();
});

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Panoya kopyalandı!', 'success');
    });
}

// AJAX form submit
function ajaxFormSubmit(formId, callback) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> İşleniyor...';
        }
        
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (callback) callback(data);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || 'Kaydet';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Bir hata oluştu!', 'danger');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || 'Kaydet';
            }
        });
    });
}

console.log('Admin panel yüklendi!');
