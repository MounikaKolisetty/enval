import { Component } from '@angular/core';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';
import { FooterComponent } from '../footer/footer.component';
import { NavbarComponent } from '../navbar/navbar.component';

@Component({
  selector: 'app-shipping-delivery',
  standalone: true,
  imports: [ScrollButtonComponent,
            FooterComponent,
            NavbarComponent
  ],
  templateUrl: './shipping-delivery.component.html',
  styleUrl: './shipping-delivery.component.css'
})
export class ShippingDeliveryComponent {

}
