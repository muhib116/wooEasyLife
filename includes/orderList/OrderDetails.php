<style>
    #woo_easy_life_order_preview_popup_wrapper{
        background-color: #3335;
        padding: 30px;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;

        .woo_easy_popup_content{
            max-width: 900px;
            width: 100%;
            height: 80vh;
            background-color: #fff;
            position: relative;

            .woo_easy_close-popup{
                position: absolute;
                right: 10px;
                top: 10px;
                font-size: 30px;
                width: 30px;
                height: 30px;
                display: block;
                line-height: 10px;
                background: transparent;
                border: none;
                font-weight: 200;
                cursor: pointer;
            }
            .woo_easy_close-popup:hover{
                color: red;
            }

            .woo_easy_header {
                padding: 15px 20px;
                border-bottom: 1px solid #c1c1c1;
                h3 {
                    margin: 0;
                    font-size: 18px;
                    font-weight: bold;
                }
            }

            #woo_easy_life_order_details{
                padding: 20px;
                .woo_easy_customer_details{
                    h3.title{
                        font-weight: 600;
                        margin: 0;
                        margin-bottom: 10px;
                    }
                    h4{
                        margin: 0;
                        font-weight: 300;
                    }
                    .woo_easy_customer_info{
                        display: grid;
                        grid-template-columns: 172px 1fr;
                        gap: 4px;
                    }
                    .woo_easy_order_list{
                        table{
                            width: 100%;
                            border-collapse: collapse;
                        }
                        table, td, th {
                            border: 1px solid #c1c1c1;
                        }
                    }
                }
            }
        }
    }
</style>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

<div id="woo_easy_life_order_preview_popup_wrapper">
    <div class="woo_easy_popup_content">
        <button class="woo_easy_close-popup">×</button>
        <div class="woo_easy_header">
            <h3>Duplicate Order History</h3>
        </div>
        <div id="woo_easy_life_order_details">
            <div class="woo_easy_customer_details">
                <h3 class="title">Customer Details</h3>
                <div class="woo_easy_customer_info">
                    <h4><span style="font-weight: bold;">Name:</span> Customer Name</h4>
                    <h4><span style="font-weight: bold;">Phone:</span> +880988476478</h4>
                    <h4><span style="font-weight: bold;">Email:</span> +880988476478</h4>
                    <h4><span style="font-weight: bold;">Address:</span> +880988476478</h4>
                </div>

                <div class="woo_easy_order_list">
                    <table class="wp-list-table widefat fixed striped table-view-list orders wc-orders-list-table wc-orders-list-table-shop_order">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#4545 Muhib</td>
                                <td>Des 10, 2024</td>
                                <td>Processing</td>
                                <td>1200Taka</td>
                                <td><button>View</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
  const { createApp, ref } = Vue

  createApp({
    setup() {
      const message = ref('Hello vue!')
      return {
        message
      }
    }
  }).mount('#woo_easy_life_order_preview_popup_wrapper')
</script>