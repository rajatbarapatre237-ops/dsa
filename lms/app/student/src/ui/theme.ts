import { PRIMARY, PRIMARY_DARK } from '../config';

export const theme = {
  primary: PRIMARY,
  primaryDark: PRIMARY_DARK,
  bg: '#eef3f9',
  card: '#ffffff',
  text: '#0f172a',
  muted: '#64748b',
  danger: '#dc2626',
  success: '#16a34a',
  warning: '#d97706',
  border: '#e2e8f0',
  primarySoft: '#e8f2fc',
  primaryMuted: '#dbeafe',
  shadow: '#0f172a',
  accentBlue: '#e8f2fc',
  accentGreen: '#ecfdf3',
  accentPurple: '#ede9fe',
  accentAmber: '#fef3c7',
};

export const cardStyle = {
  backgroundColor: theme.card,
  borderRadius: 20,
  padding: 18,
  marginBottom: 14,
  borderWidth: 1,
  borderColor: theme.border,
  shadowColor: theme.shadow,
  shadowOffset: { width: 0, height: 8 },
  shadowOpacity: 0.06,
  shadowRadius: 14,
  elevation: 3,
} as const;
