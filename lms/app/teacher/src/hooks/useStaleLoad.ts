import { useCallback, useRef, useState } from 'react';

type LoadOptions = {
  showRefresh?: boolean;
};

export function useStaleLoad() {
  const hasDataRef = useRef(false);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const beginLoad = useCallback((options?: LoadOptions) => {
    if (hasDataRef.current) {
      if (options?.showRefresh) {
        setRefreshing(true);
      }
    } else {
      setLoading(true);
    }
  }, []);

  const endLoad = useCallback(() => {
    setLoading(false);
    setRefreshing(false);
  }, []);

  const markHasData = useCallback(() => {
    hasDataRef.current = true;
  }, []);

  return { loading, refreshing, beginLoad, endLoad, markHasData };
}
