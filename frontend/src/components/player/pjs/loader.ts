export default new Promise((res) => {
  const script = document.createElement('script');
  script.onload = () => res(null);
  script.setAttribute('src', '/src/playerjs.js');
  document.head.appendChild(script);
});
