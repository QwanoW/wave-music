import player from '@/playerjs.js?url';

export default new Promise((res) => {
  const script = document.createElement('script');
  script.onload = () => res(null);
  script.setAttribute('src', player);
  document.head.appendChild(script);
});
