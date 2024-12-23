import { Injectable } from '@angular/core';
import { UserService } from './user.service';
declare var Razorpay: any;
@Injectable({
  providedIn: 'root',
})
export class PaymentService {
  private razorpayOptions: any;

  constructor(private userService: UserService) {
    this.razorpayOptions = {
      key: 'rzp_test_GipqMzLCho9rLk',
      amount: 0, // Will be updated later
      currency: 'INR',
      name: 'ENVAL',
      image: '/assets/logo.png',
      description: 'Purchase Description',
      handler: (response: any) => {
        console.log(response);
        this.verifyPayment(response.razorpay_payment_id, response.razorpay_order_id, response.razorpay_signature);
      },
      prefill: { 
        name: '', // Initialize with empty string 
        email: '', // Initialize with empty string 
        contact: '', // Initialize with empty string 
      },
      notes: {},
      theme: {
        color: '#002147',
      },
    };
  }

  setOrderDetails(amount: number, userName: string, userEmail: string, userContact: string, orderId: string): void {
    this.razorpayOptions.amount = amount;
    this.razorpayOptions.prefill.name = userName;
    this.razorpayOptions.prefill.email = userEmail;
    this.razorpayOptions.prefill.contact = userContact;
    this.razorpayOptions.order_id = orderId;
  }

  openRazorpay() {
    const rzp = new Razorpay(this.razorpayOptions);
    rzp.open();
  }

  verifyPayment(paymentId: string, orderId: string, signature: string) { 
    const verifyDetails = { 
      razorpay_payment_id: paymentId, 
      razorpay_order_id: orderId, 
      razorpay_signature: signature, 
    }; 
    
    this.userService.verifyPayment(verifyDetails).subscribe( 
      response => { 
        console.log('Payment verification response:', response); 
      }, 
      error => { 
        console.error('Error verifying payment:', error); 
      } 
    ); 
  }
}
