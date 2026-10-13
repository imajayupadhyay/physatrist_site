import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Shared between the Services/Assessment sections and the appointment form:
// "Book a session" links pre-select the matching concern in the form.
// Options come from the server (config/leads.php) via the Home page's `appointment` prop.
const concern = ref('Not sure yet');

export function useAppointment() {
    const page = usePage();
    const concernOptions = computed(() => page.props.appointment?.concerns ?? []);
    const modeOptions = computed(() => page.props.appointment?.modes ?? []);

    const selectConcern = (value) => {
        if (concernOptions.value.includes(value)) {
            concern.value = value;
        }
    };

    return { concern, concernOptions, modeOptions, selectConcern };
}
