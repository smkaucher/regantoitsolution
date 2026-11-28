import { useLayoutEffect } from 'react';

/** Runs page behaviours (init(root) -> cleanup, see behaviors.js) on the element in ref once it is in the page,
 *  and cleans them up when it leaves. deps re-run them (e.g. when deferred data arrives). */
export default function useBehaviors(ref, inits, deps = []) {
    useLayoutEffect(() => {
        const root = ref.current;
        if (!root) return undefined;
        const cleanups = inits.map((init) => init(root));
        return () => cleanups.forEach((c) => c && c());
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, deps);
}
