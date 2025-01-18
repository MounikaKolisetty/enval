import { Injectable } from '@angular/core';
import { UserService } from './user.service';
declare var Razorpay: any;
@Injectable({
  providedIn: 'root',
})
export class PaymentService {
  private razorpayOptions: any;
  private userDetails: any;
  paymentsuccess: boolean = false;

  constructor(private userService: UserService) {
    this.razorpayOptions = {
      key: 'rzp_live_c061E9tG8H1iyX',
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

  setOrderDetails(amount: number, userName: string, userEmail: string, userContact: string, orderId: string, courseTitle: string): void {
    this.razorpayOptions.amount = amount;
    this.razorpayOptions.prefill.name = userName;
    this.razorpayOptions.prefill.email = userEmail;
    this.razorpayOptions.prefill.contact = userContact;
    this.razorpayOptions.order_id = orderId;
    this.razorpayOptions.notes = { courseTitle: courseTitle };
  }

  openRazorpay() {
    const rzp = new Razorpay(this.razorpayOptions);
    rzp.open();
  }

  processUserDetails(userDetails: any) {
    this.userDetails = userDetails;
  }

  verifyPayment(paymentId: string, orderId: string, signature: string) { 
    const verifyDetails = { 
      razorpay_payment_id: paymentId, 
      razorpay_order_id: orderId, 
      razorpay_signature: signature, 
      course_title: this.razorpayOptions.notes.courseTitle,
      user_email: this.razorpayOptions.prefill.email
    }; 
    
    this.userService.verifyPayment(verifyDetails).subscribe( 
      response => { 
        console.log('Payment verification response:', response);
        this.paymentsuccess = true 
        this.userService.saveToDb( verifyDetails,  this.userDetails, this.paymentsuccess).subscribe(
          saveResponse => { 
            console.log('User details saved to database:', saveResponse); 
          }, 
          saveError => { 
            console.error('Error saving user details:', saveError); 
          }
        )
      }, 
      error => { 
        console.error('Error verifying payment:', error); 
      } 
    ); 
  }
}
