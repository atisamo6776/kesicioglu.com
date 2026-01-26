// ===================================
// Hece Sayma Programı - Vanilla JS
// ===================================

// Tag Data
const TAG_DATA = [
    {
        title: "Essential Structure Tags",
        groups: [{
            title: "",
            tags: [
                { label: "[Verse]", tag: "[Verse]", description: "Main narrative section" },
                { label: "[Chorus]", tag: "[Chorus]", description: "Memorable hook section" },
                { label: "[Bridge]", tag: "[Bridge]", description: "Contrasting section" },
                { label: "[Intro]", tag: "[Intro]", description: "Opening section" },
                { label: "[Outro]", tag: "[Outro]", description: "Closing section" },
                { label: "[Pre-Chorus]", tag: "[Pre-Chorus]", description: "Build-up before chorus" },
                { label: "[Post-Chorus]", tag: "[Post-Chorus]", description: "After chorus section" },
            ]
        }]
    },
    {
        title: "Advanced Structures",
        groups: [{
            title: "",
            tags: [
                { label: "[Verse 1]", tag: "[Verse 1]", description: "Numbered verses" },
                { label: "[Verse 2]", tag: "[Verse 2]", description: "Numbered verses" },
                { label: "[Chorus x2]", tag: "[Chorus x2]", description: "Repeat indicators" },
                { label: "[Instrumental Break]", tag: "[Instrumental Break]", description: "No vocals section" },
                { label: "[Solo Section]", tag: "[Solo Section]", description: "Featured instrument spotlight" },
            ]
        }]
    },
    {
        title: "🌙 Mood & Atmosphere",
        groups: [
            {
                title: "Emotional Moods",
                tags: [
                    { label: "[Melancholic]", tag: "[Melancholic]", description: "Sad, reflective" },
                    { label: "[Euphoric]", tag: "[Euphoric]", description: "Extremely happy, uplifting" },
                    { label: "[Nostalgic]", tag: "[Nostalgic]", description: "Wistful, remembering" },
                    { label: "[Dreamy]", tag: "[Dreamy]", description: "Ethereal, floating" },
                    { label: "[Aggressive]", tag: "[Aggressive]", description: "Intense, forceful" },
                    { label: "[Peaceful]", tag: "[Peaceful]", description: "Calm, serene" },
                    { label: "[Mysterious]", tag: "[Mysterious]", description: "Enigmatic, intriguing" },
                ]
            },
            {
                title: "Atmospheric Tags",
                tags: [
                    { label: "[Dark Atmosphere]", tag: "[Dark Atmosphere]", description: "Brooding, ominous" },
                    { label: "[Bright Atmosphere]", tag: "[Bright Atmosphere]", description: "Light, cheerful" },
                    { label: "[Ambient Atmosphere]", tag: "[Ambient Atmosphere]", description: "Spacious, atmospheric" },
                    { label: "[Intimate Atmosphere]", tag: "[Intimate Atmosphere]", description: "Close, personal" },
                ]
            }
        ]
    },
    {
        title: "⚡ Energy & Intensity",
        groups: [
            {
                title: "Energy Levels",
                tags: [
                    { label: "[High Energy]", tag: "[High Energy]", description: "Pumping, driving" },
                    { label: "[Medium Energy]", tag: "[Medium Energy]", description: "Steady, moderate" },
                    { label: "[Low Energy]", tag: "[Low Energy]", description: "Calm, relaxed" },
                    { label: "[Building Energy]", tag: "[Building Energy]", description: "Gradually increasing" },
                    { label: "[Explosive Energy]", tag: "[Explosive Energy]", description: "Sudden bursts" },
                ]
            },
            {
                title: "Intensity Modifiers",
                tags: [
                    { label: "[Intense]", tag: "[Intense]", description: "Maximum power" },
                    { label: "[Gentle]", tag: "[Gentle]", description: "Soft approach" },
                    { label: "[Powerful]", tag: "[Powerful]", description: "Strong presence" },
                    { label: "[Subtle]", tag: "[Subtle]", description: "Understated" },
                    { label: "[Dynamic]", tag: "[Dynamic]", description: "Varying levels" },
                ]
            }
        ]
    }
];

// Hece Sayma Algoritması
const TURKISH_VOWELS = new Set(['a', 'e', 'ı', 'i', 'o', 'ö', 'u', 'ü']);

function countSyllablesTR(text) {
    if (!text || text.trim().length === 0) {
        return 0;
    }

    const normalized = text.toLocaleLowerCase('tr');
    const cleaned = normalized.replace(/[^a-züğışöçİ\s]/gi, ' ');
    const tokens = cleaned.split(/\s+/).filter(token => token.length > 0);

    let totalSyllables = 0;

    for (const token of tokens) {
        if (!/[a-züğışöçİ]/i.test(token)) {
            continue;
        }

        let syllableCount = 0;
        for (const char of token) {
            if (TURKISH_VOWELS.has(char)) {
                syllableCount++;
            }
        }

        totalSyllables += syllableCount;
    }

    return totalSyllables;
}

// Global state
let editorText = '';
let openSections = new Set(["Essential Structure Tags"]);
const MIN_LINES = 20;

// DOM Elements
const textEditor = document.getElementById('textEditor');
const lineNumbers = document.getElementById('lineNumbers');
const syllableCounter = document.getElementById('syllableCounter');
const zebraBackground = document.getElementById('zebraBackground');
const totalSyllablesEl = document.getElementById('totalSyllables');
const totalLinesEl = document.getElementById('totalLines');
const nonEmptyLinesEl = document.getElementById('nonEmptyLines');
const copyBtn = document.getElementById('copyBtn');
const clearBtn = document.getElementById('clearBtn');
const copyMessage = document.getElementById('copyMessage');
const tagsContainer = document.getElementById('tagsContainer');

// Render Tag Sidebar
function renderTagSidebar() {
    tagsContainer.innerHTML = '';
    
    TAG_DATA.forEach(section => {
        const sectionDiv = document.createElement('div');
        sectionDiv.className = 'tag-section';
        
        // Section header
        const header = document.createElement('button');
        header.className = 'section-header';
        header.innerHTML = `
            <span class="section-title">${section.title}</span>
            <i class="fas fa-chevron-${openSections.has(section.title) ? 'down' : 'right'}"></i>
        `;
        header.onclick = () => toggleSection(section.title);
        
        sectionDiv.appendChild(header);
        
        // Section content
        if (openSections.has(section.title)) {
            const content = document.createElement('div');
            content.className = 'section-content open';
            
            section.groups.forEach(group => {
                group.tags.forEach(tag => {
                    const tagBtn = document.createElement('button');
                    tagBtn.className = 'tag-btn';
                    tagBtn.innerHTML = `
                        <div class="tag-label">${tag.label}</div>
                        <div class="tag-description">${tag.description}</div>
                    `;
                    tagBtn.onclick = () => insertTag(tag.tag);
                    content.appendChild(tagBtn);
                });
            });
            
            sectionDiv.appendChild(content);
        }
        
        tagsContainer.appendChild(sectionDiv);
    });
}

// Toggle section
function toggleSection(title) {
    if (openSections.has(title)) {
        openSections.delete(title);
    } else {
        openSections.add(title);
    }
    renderTagSidebar();
}

// Insert tag at cursor position
function insertTag(tag) {
    const start = textEditor.selectionStart;
    const end = textEditor.selectionEnd;
    const textBefore = editorText.substring(0, start);
    const textAfter = editorText.substring(end);
    
    editorText = textBefore + tag + textAfter;
    textEditor.value = editorText;
    
    // Move cursor after tag
    const newCursorPos = start + tag.length;
    setTimeout(() => {
        textEditor.focus();
        textEditor.setSelectionRange(newCursorPos, newCursorPos);
    }, 0);
    
    updateEditor();
}

// Update editor display
function updateEditor() {
    const lines = editorText.split('\n');
    const displayLines = Math.max(lines.length, MIN_LINES);
    
    // Update line numbers
    lineNumbers.innerHTML = '';
    for (let i = 0; i < displayLines; i++) {
        const lineDiv = document.createElement('div');
        lineDiv.className = 'line-number';
        lineDiv.textContent = i < lines.length ? i + 1 : '';
        lineNumbers.appendChild(lineDiv);
    }
    
    // Update zebra background
    zebraBackground.innerHTML = '';
    for (let i = 0; i < displayLines; i++) {
        const zebraDiv = document.createElement('div');
        zebraDiv.className = 'zebra-line';
        zebraBackground.appendChild(zebraDiv);
    }
    
    // Update syllable counter
    syllableCounter.innerHTML = '';
    for (let i = 0; i < displayLines; i++) {
        const syllableDiv = document.createElement('div');
        syllableDiv.className = 'syllable-count';
        
        if (i < lines.length) {
            const count = countSyllablesTR(lines[i]);
            if (count > 0) {
                syllableDiv.textContent = count;
                syllableDiv.classList.add('has-value');
            } else {
                syllableDiv.textContent = '—';
            }
        }
        
        syllableCounter.appendChild(syllableDiv);
    }
    
    // Update stats
    const totalSyllables = lines.reduce((sum, line) => sum + countSyllablesTR(line), 0);
    const nonEmptyLines = lines.filter(line => line.trim().length > 0).length;
    
    totalSyllablesEl.textContent = totalSyllables;
    totalLinesEl.textContent = lines.length;
    nonEmptyLinesEl.textContent = nonEmptyLines;
    
    // Adjust textarea height
    const LINE_HEIGHT = 24;
    const PADDING = 12;
    const editorHeight = displayLines * LINE_HEIGHT + (PADDING * 2);
    textEditor.style.height = `${editorHeight}px`;
}

// Event listeners
textEditor.addEventListener('input', (e) => {
    editorText = e.target.value;
    updateEditor();
});

textEditor.addEventListener('keydown', (e) => {
    // Ctrl+Z / Ctrl+Y handled by browser default
    // You can add custom keyboard shortcuts here if needed
});

copyBtn.addEventListener('click', () => {
    navigator.clipboard.writeText(editorText).then(() => {
        copyMessage.classList.add('show');
        setTimeout(() => {
            copyMessage.classList.remove('show');
        }, 2000);
    });
});

clearBtn.addEventListener('click', () => {
    if (confirm('Tüm metni silmek istediğinize emin misiniz?')) {
        editorText = '';
        textEditor.value = '';
        updateEditor();
        textEditor.focus();
    }
});

// Initialize
renderTagSidebar();
updateEditor();

console.log('Hece Sayma Programı yüklendi!');
