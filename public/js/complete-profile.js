// Complete Profile Page JS
(function(){
  const form = document.getElementById('completeProfileForm');
  const avatarInput = document.getElementById('avatar');
  const chooseAvatar = document.getElementById('chooseAvatar');
  const removeAvatar = document.getElementById('removeAvatar');
  const avatarPreview = document.getElementById('avatarPreview');
  const bio = document.getElementById('bio');
  const bioCount = document.getElementById('bioCount');
  const chipsContainer = document.getElementById('interestsChips');
  const selectedCount = document.getElementById('selectedCount');

  const INTERESTS = [
    'Technology','Design','Business','Health','Travel','Food','Lifestyle','Entertainment','Education','Science','Writing','Photography','Gaming','Finance','Productivity'
  ];
  const MAX_SELECTION = 5;
  const MAX_BIO = 200;

  function initChips(){
    chipsContainer.innerHTML = '';
    INTERESTS.forEach(label => {
      const chip = document.createElement('button');
      chip.type = 'button';
      chip.className = 'chip';
      chip.textContent = label;
      chip.setAttribute('data-value', label);
      chip.addEventListener('click', () => toggleChip(chip));
      chipsContainer.appendChild(chip);
    });
  }

  function getSelectedChips(){
    return Array.from(chipsContainer.querySelectorAll('.chip.selected')).map(c => c.getAttribute('data-value'));
  }

  function toggleChip(chip){
    const selected = getSelectedChips();
    if (chip.classList.contains('selected')) {
      chip.classList.remove('selected');
    } else {
      if (selected.length >= MAX_SELECTION) return;
      chip.classList.add('selected');
    }
    selectedCount.textContent = getSelectedChips().length;
  }

  function updateBioCount(){
    const val = (bio.value || '').slice(0, MAX_BIO);
    if (val !== bio.value) bio.value = val;
    bioCount.textContent = val.length;
  }

  function handleAvatarPreview(file){
    if (!file) return;
    const allowed = ['image/jpeg','image/png','image/jpg'];
    if (!allowed.includes(file.type)) {
      alert('Please upload a JPG or PNG image.');
      avatarInput.value = '';
      return;
    }
    if (file.size > 2 * 1024 * 1024) { // 2MB
      alert('Image must be smaller than 2MB.');
      avatarInput.value = '';
      return;
    }
    const reader = new FileReader();
    reader.onload = e => {
      avatarPreview.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }

  // Event bindings
  chooseAvatar?.addEventListener('click', () => avatarInput?.click());
  removeAvatar?.addEventListener('click', () => {
    avatarInput.value = '';
    avatarPreview.src = '/StoryHub/public/images/default-avatar.jpg';
  });
  avatarInput?.addEventListener('change', (e) => handleAvatarPreview(e.target.files?.[0]));

  bio?.addEventListener('input', updateBioCount);
  updateBioCount();

  initChips();
  selectedCount.textContent = '0';

  form?.addEventListener('submit', (e) => {
    e.preventDefault();
    // Mock submit: collect data and simulate saving
    const data = {
      bio: bio.value.trim(),
      interests: getSelectedChips(),
      hasAvatar: !!(avatarInput && avatarInput.files && avatarInput.files.length)
    };
    const btn = form.querySelector('button[type="submit"]');
    const text = btn.querySelector('.btn-text');
    const loader = btn.querySelector('.btn-loader');
    text.style.display = 'none';
    loader.style.display = 'inline-block';
    setTimeout(() => {
      loader.style.display = 'none';
      text.style.display = 'inline';
      // For now, redirect to profile page (view not implemented here)
      window.location.href = '/StoryHub/index.php?url=profile';
    }, 1200);
  });
})();
