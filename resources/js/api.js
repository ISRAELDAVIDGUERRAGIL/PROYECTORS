export function crearClienteAPI(baseUrl = '/api') {
  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };

  function csrf() {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
  }

  async function request(url, options = {}) {
    if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(options.method)) {
      headers['X-XSRF-TOKEN'] = csrf();
    }
    const res = await fetch(baseUrl + url, { ...options, headers });
    if (!res.ok) {
      const body = await res.json().catch(() => ({}));
      throw body;
    }
    return res.json();
  }

  return {
    listar: (r, params = '') => request(`/${r}${params}`),
    crear: (r, datos) => request(`/${r}`, { method: 'POST', body: JSON.stringify(datos) }),
    obtener: (r, id) => request(`/${r}/${id}`),
    actualizar: (r, id, datos) => request(`/${r}/${id}`, { method: 'PUT', body: JSON.stringify(datos) }),
    eliminar: (r, id) => request(`/${r}/${id}`, { method: 'DELETE' }),
  };
}
