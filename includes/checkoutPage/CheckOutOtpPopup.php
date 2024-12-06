<link rel="stylesheet" href="<?php echo plugin_dir_url(__DIR__) . 'checkoutPage/style.css'; ?>">

<div class="woo_easy_life_modal_wraper lg" id="woo_easy_modal">
    <div class="modal_container" style="color: #555 !important;">
        <div class="modal_header" style="display: block;">
            <h4 
                style="
                    font-size: 20px;
                    text-align: center;
                    font-weight: bold;
                "
            >
                Verify your phone number
            </h4>
        </div>
        <div class="modal_body text-center">
            <h4 
                style="
                    font-weight: 500;
                    text-align: center;
                    margin-bottom: 20px;
                    font-size: 16px;
                "
            >
                An OTP has been sent to 
                <span style="font-weight: bold;color: #ff4242;">01853S25141.</span>
            </h4>

            <div style="display: grid; justify-content: center; text-align: center;">
                <span style="font-weight: bold; font-size: 18px; margin-bottom:6px;">
                    Enter OTP Code
                </span>
                <input
                    style="
                        padding: 6px 6px;
                        font-size: 20px;
                        text-align: center;
                        border: 1px solid #ff7a00;
                        border-radius: 2px;
                        background: #3331;
                        box-shadow: 2px 2px 2px #4444 inset;
                        font-weight: bold;
                        letter-spacing: 20px;
                    "
                    placeholder=""
                />
            </div>

            <br />
            <p style="text-align: center; font-weight: bold;">
                Didn’t receive the code?
            </p>
            <p style="text-align: center;color: #888;">
                You can resend it {{ getCountDownData }}
            </p>

            <div    
                style="
                    display: flex;
                    place-items: center;
                    justify-content: center;
                    gap: 20px;
                    margin-top: 10px;
                "
            >
                <button
                    v-if="remainingTime <= 0"
                    style="
                        background-color: #2196F3;
                        color: #fff;
                        padding: 8px 15px;
                        border-radius: 4px;
                        box-shadow: 0 2px 10px #0003;
                        cursor: pointer;
                        border: none;
                    "
                    @click="resendOTP"
                >Resend OTP</button>
                <button
                    type="submit" 
                    class="button alt custom-class" 
                    name="woocommerce_checkout_place_order" 
                    id="place_order" 
                    value="Place Order Now"
                >Place Order</button>
            </div>
        </div>

        <div class="modal_footer">
            Secure your order
            <button class="close btn">Close</button>
        </div>
    </div>
</div>


<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="<?php echo plugin_dir_url(__DIR__) . 'checkoutPage/popup.js'; ?>"></script>

<script>
    const { createApp, ref, onMounted, computed } = Vue
    createApp({
        setup() {
            const resendOtpCountDownTimeInSecond = 12;
            const remainingTime = ref(resendOtpCountDownTimeInSecond);
            const minutes = ref(Math.floor(remainingTime.value / 60))
            const seconds = ref(remainingTime.value % 60);
            const displayCount = ref('00:00');

            const woo_easy_life_startCountdown = () => {
                remainingTime.value = resendOtpCountDownTimeInSecond

                const interval = setInterval(() => {
                    minutes.value = Math.floor(remainingTime.value / 60);
                    seconds.value = remainingTime.value % 60;

                    displayCount.value = `in ${minutes.value}:${seconds.value < 10 ? '0' : ''}${seconds.value}`;

                    if (remainingTime.value <= 0) {
                        clearInterval(interval);
                    }

                    remainingTime.value--;
                }, 1000);
            }

            const resendOTP = () => {
                woo_easy_life_startCountdown()
            }

            const getCountDownData = computed(() => {
                return remainingTime.value > 0 ? `in ${displayCount.value}` : 'now'
            })

            return {
                resendOtpCountDownTimeInSecond,
                remainingTime,
                minutes,
                seconds,
                displayCount,
                resendOTP,
                woo_easy_life_startCountdown,
                getCountDownData
            }
        }
    }).mount('#woo_easy_modal')
</script>