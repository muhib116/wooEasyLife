import { computed, ref } from "vue"

export const useRecharge = () => {
    const data = [
        {
            logo: 'https://wpsalehub.com/wp-content/uploads/2025/01/image-14.webp',
            paymentPartner: 'bKash',
            bg: '#e4136b22',
            fee: 1.85, //in percent
            note: 'bKash "Send Money" fee will be added with net price.',
            instructions: ` 
                <p>01. Go to your bKash app or Dial *247#</p> 
                <p>02. Choose “Send Money”</p> 
                <p>03. Enter below bKash Account Number</p> 
                <p>04. Enter total amount</p> 
                <p>06. Now enter your bKash Account PIN to confirm the transaction</p> 
                <p>07. Copy Transaction ID from payment confirmation message and paste that Transaction ID below</p> 
            `,
            accountType: 'Personal',
            account: '01770-989591'
        },
        {
            logo: 'https://wpsalehub.com/wp-content/uploads/2025/01/rocket.webp',
            paymentPartner: 'Rocket',
            bg: '#8e349322',
            fee: 1.8, //in percent
            note: 'Rocket "Send Money" fee will be added with net price.',
            instructions: `
                <p>01. Go to your Rocket app or Dial *322#</p>
                <p>02. Choose “Send Money”</p>
                <p>03. Enter below Rocket Account Number</p>
                <p>04. Enter <b>total amount</b></p>
                <p>06. Now enter your Rocket Account PIN to confirm the transaction</p>
                <p>07. Copy Transaction ID from payment confirmation message and paste that Transaction ID below</p>
            `,
            accountType: 'Personal',
            account: '01770-989591-9'
        }
    ]

    const selectedPaymentGetaway = ref<{paymentPartner: string} | null>(null)

    const form = ref<{
        payableAmount: number
        rechargeableAmount: number | null
        transactionId: string
        accountNumber: string
    }>({
        payableAmount: 0,
        rechargeableAmount: null,
        transactionId: '',
        accountNumber: '',
    })

    const payableAmount = (fee: number) => {
        let amount = 0
        if(form.value.rechargeableAmount){
            amount = form.value.rechargeableAmount + (form.value.rechargeableAmount * (fee / 100))
        }

        amount = Math.round(amount)
        form.value.payableAmount = amount
        return amount
    }

    return {
        data,
        form,
        selectedPaymentGetaway,
        payableAmount
    }
}