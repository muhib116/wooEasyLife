export const getContrastColor = (hexColor: string) => {
    // Remove the hash symbol if present
    hexColor = hexColor.replace('#', '')

    // If shorthand hex, convert to full form
    if(hexColor.length === 3) {
        hexColor = hexColor
            .split('')
            .map(char => char + char)
            .join('')
    }

    // Parse RGB values
    const r = parseInt(hexColor.substring(0, 2), 16)
    const g = parseInt(hexColor.substring(2, 4), 16)
    const b = parseInt(hexColor.substring(4, 6), 16)

    // Calculate relative luminance
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255

    // Return white (#ffffff) for dark backgrounds, black (#000000) for light backgrounds
    return luminance > 0.5 ? '#000000' : '#ffffff'
}

export const generateSlug = (title: string) => {
    return title
        .toLowerCase() // Convert to lowercase
        .trim() // Remove leading and trailing whitespace
        .replace(/&/g, 'and') // Replace '&' with 'and'
        .replace(/[^a-z0-9 -]/g, '') // Remove invalid characters
        .replace(/\s+/g, '-') // Replace spaces with hyphens
        .replace(/-+/g, '-') // Collapse multiple hyphens into one
        .replace(/^-+|-+$/g, ''); // Remove leading and trailing hyphens
}