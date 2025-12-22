(function () {
  const base = (typeof window.APP_BASE === 'string') ? window.APP_BASE : '/';

  function el(tag, attrs = {}, children = []) {
    const node = document.createElement(tag);
    for (const [k, v] of Object.entries(attrs)) {
      if (k === 'class') node.className = v;
      else if (k === 'text') node.textContent = v;
      else node.setAttribute(k, v);
    }
    for (const child of children) node.appendChild(child);
    return node;
  }

  const state = { open: false, history: [] };

  // UI
  const panel = el('div', { class: 'chatbot-panel', id: 'chatbot-panel' });
  const header = el('div', { class: 'chatbot-header' });
  const title = el('div', { class: 'chatbot-title' }, [
    el('i', { class: 'fa-solid fa-wand-magic-sparkles' }),
    el('span', { text: 'StoryHub AI' })
  ]);
  const closeBtn = el('button', { class: 'chatbot-close', type: 'button', 'aria-label': 'Close chat' });
  closeBtn.innerHTML = '&times;';
  header.appendChild(title);
  header.appendChild(closeBtn);

  const messages = el('div', { class: 'chatbot-messages', id: 'chatbot-messages' });
  const form = el('form', { class: 'chatbot-form' });
  const input = el('input', { class: 'chatbot-input', type: 'text', placeholder: 'Ask me anything…', autocomplete: 'off' });
  const sendBtn = el('button', { class: 'chatbot-send', type: 'submit', text: 'Send' });
  form.appendChild(input);
  form.appendChild(sendBtn);

  const hint = el('div', { class: 'chatbot-hint', text: 'Tip: Ask about writing, publishing, likes/saves/comments, or profiles.' });

  panel.appendChild(header);
  panel.appendChild(messages);
  panel.appendChild(form);
  panel.appendChild(hint);

  const toggle = el('button', { class: 'chatbot-toggle', type: 'button', 'aria-label': 'Open chat' });
  toggle.innerHTML = '<i class="fa-solid fa-comment-dots"></i>';

  const root = el('div', { class: 'chatbot-widget' }, [panel, toggle]);

  function addMessage(role, text) {
    const msg = el('div', { class: `chatbot-msg ${role === 'user' ? 'user' : 'bot'}` });
    msg.textContent = text;
    messages.appendChild(msg);
    messages.scrollTop = messages.scrollHeight;
  }

  function setOpen(open) {
    state.open = open;
    panel.classList.toggle('open', open);
    if (open) {
      if (state.history.length === 0) {
        addMessage('bot', 'Hi! I’m your StoryHub assistant. How can I help?');
      }
      input.focus();
    }
  }

  async function sendMessage(text) {
    state.history.push({ role: 'user', text });
    addMessage('user', text);

    const typingId = 'typing-' + Math.random().toString(16).slice(2);
    const typing = el('div', { class: 'chatbot-msg bot', id: typingId, text: 'Typing…' });
    messages.appendChild(typing);
    messages.scrollTop = messages.scrollHeight;

    try {
      const res = await fetch(`${base}index.php?url=api-chat`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({
          message: text,
          history: state.history.map(m => ({ role: m.role === 'model' ? 'model' : 'user', text: m.text })),
        }),
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(data.error || 'Chat failed');

      document.getElementById(typingId)?.remove();
      const reply = (data.reply || '').toString().trim() || '…';
      state.history.push({ role: 'model', text: reply });
      addMessage('bot', reply);
    } catch (e) {
      document.getElementById(typingId)?.remove();
      addMessage('bot', `Error: ${e.message}`);
    }
  }

  toggle.addEventListener('click', () => setOpen(!state.open));
  closeBtn.addEventListener('click', () => setOpen(false));

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const text = input.value.trim();
    if (!text) return;
    input.value = '';
    await sendMessage(text);
  });

  document.addEventListener('DOMContentLoaded', () => {
    document.body.appendChild(root);
  });
})();
