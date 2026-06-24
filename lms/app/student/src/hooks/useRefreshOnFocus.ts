import { useCallback, useRef } from 'react';
import { useFocusEffect } from '@react-navigation/native';

type LoadFn = (options?: { showRefresh?: boolean }) => void | Promise<void>;

export function useRefreshOnFocus(load: LoadFn) {
  const loadRef = useRef(load);
  loadRef.current = load;

  useFocusEffect(
    useCallback(() => {
      loadRef.current({ showRefresh: false });
    }, []),
  );
}
