export function loginStudentIdError(id: string): string | null {
  if (id.trim().toUpperCase().startsWith('ACE')) {
    return 'Use DSA615 or 615 as student ID';
  }
  return null;
}
