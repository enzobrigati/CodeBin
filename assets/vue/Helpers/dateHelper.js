export function formatDate(date) {
    const rawDate = new Date(date)
    return rawDate.toLocaleDateString() + " à " + rawDate.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})
}