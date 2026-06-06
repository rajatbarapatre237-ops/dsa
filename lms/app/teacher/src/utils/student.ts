const PLACEHOLDER_BATCHES = new Set([
  '',
  'none',
  'select option',
  'select batch',
]);

export function formatStudentDisplayId(value?: string | number | null): string | null {
  const text = String(value ?? '').trim();
  if (!text) return null;

  const upper = text.toUpperCase();
  if (upper.startsWith('DSA')) return upper;
  if (upper.startsWith('ACE')) return `DSA${upper.slice(3)}`;

  return `DSA${text}`;
}

export function formatStudentBatch(batch?: string | null): string | null {
  const value = String(batch ?? '').trim();
  if (!value || PLACEHOLDER_BATCHES.has(value.toLowerCase())) {
    return null;
  }
  return value;
}

export function formatStudentSubtitle(student: {
  course_name?: string | null;
  batch?: string | null;
}): string {
  const course = String(student.course_name ?? '').trim();
  const batch = formatStudentBatch(student.batch);

  if (course && batch) return `${course} · ${batch}`;
  if (course) return course;
  if (batch) return batch;
  return 'Course not set';
}
