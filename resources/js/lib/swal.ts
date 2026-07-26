import Swal, { type SweetAlertCustomClass } from 'sweetalert2';

type SwalVariant = 'primary' | 'error' | 'success' | 'warning';

const baseClass: SweetAlertCustomClass = {
    popup: 'rounded-box border border-base-300 shadow-sm font-sans',
    title: 'text-base-content text-xl font-bold',
    htmlContainer: 'text-base-content/70',
    input: 'input input-bordered w-full',
    confirmButton: 'btn btn-primary',
    cancelButton: 'btn btn-ghost',
    actions: 'gap-2',
};

/**
 * Retorna o customClass completo com o botão de confirmação na variante
 * pedida, para usar no lugar de `confirmButtonColor` em hex.
 */
export function swalClass(variant: SwalVariant = 'primary'): SweetAlertCustomClass {
    return { ...baseClass, confirmButton: `btn btn-${variant}` };
}

export default Swal.mixin({
    buttonsStyling: false,
    reverseButtons: true,
    customClass: baseClass,
});
