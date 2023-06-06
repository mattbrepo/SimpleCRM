
export function getFiltered(items, filters) {

  return items.filter(item => {
    return Object.keys(filters).every(key =>
       filters[key] === '' || String(item[key]).toLowerCase().includes(filters[key].toLowerCase())
    );
  });
}