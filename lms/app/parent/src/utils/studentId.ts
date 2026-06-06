export function formatStudentDisplayId(value?: string | number | null): string | null {
  const text = String(value ?? '').trim();
  if (!text) return null;

  const upper = text.toUpperCase();
  if (upper.startsWith('DSA')) return upper;
  if (upper.startsWith('ACE')) return `DSA${upper.slice(3)}`;

  return `DSA${text}`;
}

export function loginStudentIdError(id: string): string | null {
  if (id.trim().toUpperCase().startsWith('ACE')) {
    return 'Use DSA615 or 615 as student ID';
  }
  return null;
}
